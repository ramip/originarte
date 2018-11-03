{*
*  @author HDClic
*  @copyright  permanent www.hdclic.com
*  @version  Release: $Revision: 1.5 / 1.6 $
*}

{if isset($smarty.get.search_query)}
<!-- Module Presta Blog -->
	<script type="text/javascript">
	{literal}
		$(document).ready(function(){
			$.ajax({
				type: "GET",
				url: "{/literal}{PrestaBlogAjaxSearchUrl}{literal}?do=search&req={/literal}{$smarty.get.search_query}{literal}",
				dataType : "html",
				error:function(msg, string){ alert( "Error !: " + string ); },
				success:function(data){
					if(data)
						$("div#center_column").append(data);
				}
			});
		});
	{/literal}
	</script>
<!-- /Module Presta Blog -->
{/if}
