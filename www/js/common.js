$(function() {

    $('input[name=keyword]').focus();

    // 검색 초기화
    $('.btn-del').click(function() {
        $('input[name=keyword]').val('');
        $('ul.IdPList > li').show();
        $('input[name=keyword]').focus();
        $(this).hide();
        return false;
    });

    // 이전 히스토리에서 선택한 IDP 삭제
    $('a[data-ref=del]').click(function() {
        var data = [
            {'name':'previous_idp','value':'true'},
            {'name':'previous_selected_idp','value':$(this).data('idpkey')}
        ];
        var thiz = $(this);
        $.ajax({
            url: window.location.href,
            data: data,
            type: "post",
            async: false,
            cache: false,
            success: function(res) {
                thiz.parent().remove();
                //location.reload();
            },
            error : function(xhr, status, error) {
                alert(error);
            }
        });
        return false;
    });

    // 검색
    $('input[name=keyword]').keyup(function() {
        var keyword = $(this).val();

        if (keyword) $('.search a.btn-del').show();
        else $('.search a.btn-del').hide();

        $('ul.IdPList li').show();
        $('ul.IdPList li').each(function() {
            var target = $(this);
            if (target.find('[data-idpname]').data('idpname').toLowerCase().indexOf(keyword.toLowerCase()) === -1) {
                target.hide();
            }
        });

        return false;
    });

    // IDP 선택
    $('ul.IdPList > li').bind('click',function(e) {
        var $target = $(this);
        if (e.type == 'click') {
            var idp_key = $target.find('a').data('idpkey');
            var idp_name = $target.find('a').data('idpname');

            $('#dropdownlist').val(idp_key);
            if($('#remembercheckboxdisplay').is(":checked"))
                $('#rembercheckbox').attr('checked', 'checked');
            else
                $('#rembercheckbox').removeAttr('checked');
            $('#IdPForm').submit();
        }

        return false;
    });

    // IDP 마우스오버 효과
    $('ul.IdPList > li').hover(function() {
        $(this).addClass('on');
        if (!$(this).parent().hasClass('Previously')) {
            $(this).find('> a').prepend('<p class="icon"><img src="/simplesaml/module.php/kafedsacl/images/custom2/icon_arr.png" alt="selected" /></p>');
        }
    }, function() {
        $(this).removeClass('on');
        if (!$(this).parent().hasClass('Previously')) {
            $(this).find('p.icon:first').remove();
        }
    });

    // Why am I here
    $('a.btn-why').click(function() {
        $('#why').show();
        return false;
    });
    $('a[data-ref=close]').click(function() {
        $('#why').hide();
        return false;
    });
});
