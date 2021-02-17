$(document).ready(function() {

 $("#table-cat").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
          var rows = table.tBodies[0].rows;
          var w = "";
          for (var i = 0; i < rows.length; i++) {
            w += rows[i].id + ";";
          }

          $.ajax({
        		type: "POST",
         		url: site_url+"move-cat.php",
         		timeout: 5000,
         		data: "order=" + w,
		    
         		success: function(data){$("div#upd-dnd").html(data);},
         		error: function(data){$("div#upd-dnd").html("Error" + data);}
         	});

        }
  	});


});