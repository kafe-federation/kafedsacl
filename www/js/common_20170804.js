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

			$('input[name=user_idp]').val(idp_key);
			$('#IdPForm').submit();
		}

		return false;
	});

	$('a.btn-why').click(function() {
		$('#why').show();
		return false;
	});
	$('a[data-ref=close]').click(function() {
		$('#why').hide();
		return false;
	});
});
