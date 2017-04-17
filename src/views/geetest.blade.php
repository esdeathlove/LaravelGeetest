<script src="http://cdn.bootcss.com/jquery/2.1.0/jquery.min.js"></script>
<script src="https://static.geetest.com/static/tools/gt.js"></script>
<div id="geetest-captcha"></div>
<p id="wait" class="show">正在加载验证码...</p>
@define use Illuminate\Support\Facades\Config
<script>
    var geetest = function(url) {
        var handlerEmbed = function(captchaObj) {
            $("#geetest-captcha").closest('form').submit(function(e) {
                var validate = captchaObj.getValidate();
                if (!validate) {
                    alert('{{ Config::get('geetest.client_fail_alert')}}');
                    e.preventDefault();
                }
            });
            captchaObj.appendTo("#geetest-captcha");
            captchaObj.onSuccess(function () {
                var result = captchaObj.getValidate();
                var phone = $('#phone').val();
                // ajax 伪代码，进行二次验证
                $.post('/auth/get_sms_code', {
                    geetest_challenge: result.geetest_challenge,
                    geetest_validate: result.geetest_validate,
                    geetest_seccode: result.geetest_seccode,
                    code_type:'{{ $code_type }}',
                    phone:phone,
                    _token: '{{csrf_token()}}'
                    // 其他服务端需要的数据，比如登录时的用户名和密码
                }, function (data) {
                });
            });
            captchaObj.onReady(function() {
                $("#wait")[0].className = "hide";
            });
            if ('{{ $product }}' == 'popup') {
                captchaObj.bindOn($('#geetest-captcha').closest('form').find(':submit'));
                captchaObj.appendTo("#geetest-captcha");
            }
        };
        $.ajax({
            url: url + "?t=" + (new Date()).getTime(),
            type: "get",
            dataType: "json",
            success: function(data) {
                initGeetest({
                    gt: data.gt,
                    challenge: data.challenge,
                    product: "{{ $product }}",
                    offline: !data.success,
                    lang: '{{ Config::get('geetest.lang', 'zh-cn') }}'
                }, handlerEmbed);
            }
        });
    };
    (function() {
        geetest('{{ $geetest_url?$geetest_url:Config::get('geetest.geetest_url', 'geetest') }}');
    })();
</script>
<style>
.hide {
    display: none;
}
</style>
