

$(function () {
    var dateFormat = "mm/dd/yy";
    from = $("#fromdate")
            .datepicker({
                dateFormat: 'mm/dd/yy'
            })
            .on("change", function () {
                to.datepicker("option", "minDate", getDate(this));
            });
    to = $("#todate").datepicker({
        dateFormat: 'mm/dd/yy'
    })
            .on("change", function () {
                from.datepicker("option", "maxDate", getDate(this));
            });

    function getDate(element) {
        var date;
        try {
            date = $.datepicker.parseDate(dateFormat, element.value);
        } catch (error) {
            date = null;
        }

        return date;
    }
	
	

});

function formatStatus(status_value)
{
	switch(status_value) {
		case "1":
			return "Open";
			break;
		case "3":
			 return "Cancelled";
			break;
		case "4":
			 return "Invoiced";
			break;
		case "5":
			 return "Ready to Invoice";
			break;
		case "0":
			 return "Revised";
			break;
		default:
			 return "Open";
	}

}

function toggleStatus(tracker_id) {
    $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: "tracker_id=" + tracker_id,
        dataType: 'json',
        success: function (data) {
            if (data.status == '1')
            {
                window.location.reload();
            } else {
                alert("Try again ! ");
            }
        },
        error: function (e) {
            window.location.reload();
        }
    });
}

function escapeHtml(desc) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return desc.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function updateTable(){
 var favorite = [];
	$.each($("input[id='selected_column']:checked"), function(){            
		favorite.push($(this).val());
	});
	if(favorite.length < 1)
	{
		favorite = ["vendor", "region", "market","po_number", "line", "po_line_rev","po_date","site_name","supplier_no"
					,"description","rev","qty","unit_price","amount","status"];
	}
	favorite.push('Edit');
	console.log('orderfield:'+$('#orderfield').val());
	console.log('ordertype:'+$('#ordertype').val());
	var trackerDataVal = $('#trackerData').val();
	var duce = jQuery.parseJSON(trackerDataVal);
	$("#trackerTable").remove();
    var resultstring='<table id="trackerTable" class="r_table table-striped table-bordered tracker_list x-scroll-tbl remove-padr-15">';
      for(var j=0;j<favorite.length;j++){
              //array arr contains the field names in this case
		  var column   = toUpperCaseWords(favorite[j].replace(/\_/g,' '));
		  var sort_order = '';
		  if(favorite[j] == $('#orderfield').val())
		  {
			  if('asc' == $('#ordertype').val())
			  {
				  sort_order = 'point up_ico';
			  }else {
				  sort_order = 'point down_ico';
			  }
		  }

		  if(favorite[j] == 'site_name')
		  {
			  column = 'ID1';
		  }
		  else if(favorite[j] == 'supplier_no') {
			  column = 'ID2';
		  }
		  else if(favorite[j] == 'uco') {
			  column = 'UOM';
		  }

          resultstring+= '<th><div class="pointsec '+sort_order+ '" data-key="'+favorite[j]+'">'+ column +'</th>';
      }
	  
	  $(duce).each(function(key, value) {
		 
		 resultstring+='<tr>';
		 for(var j=0;j<favorite.length;j++){
			for (var ij in value) {
				if(ij == favorite[j]) {
				var display_value = value[ij];
						switch(ij) {
							case "status":
								display_value = formatStatus(display_value);
								break;
							case "unit_price":
								display_value = '$'+display_value;
								break;
							case "amount":
								display_value = '$'+display_value;
								break;
							case "description":
								var postFix = '';
								if(display_value.length > 38)
								{
									postFix = '...';
								}
								display_value = escapeHtml(display_value.substring(0,38))+postFix;	
								break;
							default:
								 display_value = value[ij];
						}
	
					resultstring+='<td>'+ display_value + '</td>';
					break;
				}
			}
			
		 }
		 var base_url = $('#base_url').val()+'tracker/editTracker/'+value['tracker_id'];
		 resultstring+='<td> <a href='+base_url+' class="btn_ico edit" title="Edit"></a></td>';
		 resultstring+='</tr>';
	 });
                    
     resultstring+='</table>';
     $('#trackerTableDiv').html(resultstring);
	 onPageLoad();
}

function toUpperCaseWords(string) {
    return string.replace(/\w+/g, function(str){ 
      return str[0].toUpperCase() + str.slice(1).toLowerCase();
    })
}
  

function capitalizeFirstLetter(string) {
    return string[0].toUpperCase() + string.slice(1);
}

$('.invoice input:checkbox').change(function(){
    if($(this).is(":checked")) {
		$(this).parent().addClass('select');
        //$('div.menuitem').addClass("menuitemshow");
    } else {
        //$('div.menuitem').removeClass("menuitemshow");
		$(this).parent().removeClass('select');
    }
});
	function onPageLoad(){
		
		        if(($('.chkTrak:checked').length == $('.chkTrak').length) && $('.chkTrak:checked').length > 0){
            $('#invoiceselcetallChk').prop('checked',true);
        }
        
        
        /*******************************/
        $('#invoiceselcetallChk').click(function () {
//            var val = 'N';
//            element = $(this);
//            if(element.is(':checked')){
//                val = 'Y';
//            }
//            //alert(element.is(':checked'));
//            $('#invoiceselcetall').val(val);
//            document.forms["trackerfilterform"].submit();
              $('.chkTrak').trigger('click');  
        });
        
        /******************************/
        $('.tracker_list th div.pointsec').click(function () { 
            element = $(this);
			
            $('#orderfield').val(element.data('key'));
            if (element.hasClass('up_ico')) {
                $('#ordertype').val('desc');
            } else {
                $('#ordertype').val('asc');
            }
            document.forms["trackerfilterform"].submit();
        });

        var invoicedate = $("#invoicedate").datepicker({
            dateFormat: 'mm/dd/yy'
        });

        $('.open_p').click(function () {
            invoicedate.datepicker("setDate", new Date());
            $('.popup').fadeIn();
            return false;
        });
        $('.close_p').click(function () {
            $('.popup').fadeOut();
        });
        $('.close_d').click(function () {
            $('.popup_download').fadeOut();
        });
        
        $('.chkTrak').click(function(){
                $('#invoiceselcetall').val("N");
                if(($('.chkTrak:checked').length == $('.chkTrak').length) && $('.chkTrak:checked').length > 0){
                    $('#invoiceselcetallChk').prop('checked',true);
                }else{
                    $('#invoiceselcetallChk').prop('checked',false);
                }
            //alert($(this).is(":checked"));
                $.post("<?php echo site_url('tracker/updateTrakeChk')?>",
                {
                    id: $(this).val(),
                    status: $(this).is(":checked")
                },
                function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                });
        
        });

		 $("#checkAll").click(function () {
			$("input[id='selected_column']").not(this).prop('checked', this.checked);
		});
	}
	$(function () {
		onPageLoad();
    });
	
	 $(window).bind('load', function(){
		 
		updateTable(); 
	 });