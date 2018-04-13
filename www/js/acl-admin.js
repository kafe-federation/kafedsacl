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

    // IDP 선택
    $('ul.IdPList > li').bind('click',function(e) {
        var $target = $(this);
        var href = $target.find('a').attr('href');
        location.href = location.href.split('?')[0] + href;
        console.log(href);
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
});
