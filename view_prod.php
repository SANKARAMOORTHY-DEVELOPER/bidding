<?php include 'admin/db_connect.php' ?>
<?php
session_start();
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM products where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
$cat_qry = $conn->query("SELECT * FROM categories where id = $category_id");
$category = $cat_qry->num_rows > 0 ? $cat_qry->fetch_array()['name'] : '' ;
}
?>
<style type="text/css">
	#bid-frm{
		display: none
	}
</style>
<div class="container-fluid">
	<img src="admin/assets/uploads/<?php echo $img_fname ?>" class="d-flex w-100" alt="">
	<p>Name: <large><b><?php echo $name ?></b></large></p>
	<p>Category: <b><?php echo $category ?></b></p>
	<p>Starting Amount: <b><?php echo number_format($start_bid,2) ?></b></p>
	<p>Until: <b><?php echo date("m d,Y h:i A",strtotime($bid_end_datetime)) ?></b></p>
	<p>Highest Bid: <b id="hbid"><?php echo number_format($start_bid,2) ?></b></p>
	<p>Description:</p>
	<p class=""><small><i><?php echo $description ?></i></small></p>
	<div class="col-md-12">
		<button class="btn btn-primary btn-block btn-sm" type="button" id="bid">Bid</button>
	</div>
	<div id="bid-frm">
		<div class="col-md-12">
			<form id="manage-bid">
				<input type="hidden" name="product_id" value="<?php echo $id ?>">
				<div class="form-group">
					<label for="" class="control-label">Bid Amount</label>
					<input type="number" class="form-control text-right" name="bid_amount" >
				</div>
				<div class="row justify-content-between">
					<button class="btn col-sm-5 btn-primary btn-block btn-sm mr-2">Submit</button>
					<button class="btn col-sm-5 btn-secondary mt-0 btn-block btn-sm" type="button" id="cancel_bid">Cancel</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$('#imagesCarousel img,#banner img').click(function(){
		viewer_modal($(this).attr('src'))
	})
	$('#participate').click(function(){
        _conf("Are you sure to commit that you will participate to this event?","participate",[<?php echo $id ?>],'mid-large')
    })
    var _updateBid = setInterval(function(){
    	$.ajax({
    		url:'admin/ajax.php?action=get_latest_bid',
    		method:'POST',
    		data:{product_id:'<?php echo $id ?>'},
    		success:function(resp){
    			if(resp && resp > 0){
    				$('#hbid').text(parseFloat(resp).toLocaleString('en-US',{style:'decimal',maximumFractionDigits:2,minimumFractionDigits:2}))
    			}
    		}
    	})
    },1000)

    $('#manage-bid').submit(function(e){
    	e.preventDefault()
            start_load()
            var latest = $('#hbid').text()
            latest = latest.replace(/,/g,'')
            if(parseFloat(latest)  > $('[name="bid_amount"]').val()){
            	alert_toast("Bid amount must be greater than the current Highest Bid.",'danger')
            	end_load()
            	return false;
            }
            $.ajax({
                url:'admin/ajax.php?action=save_bid',
                method:'POST',
                data:$(this).serialize(),
                success:function(resp){
                    if(resp==1){
                        alert_toast("Bid successfully submited",'success')
            			end_load()
                    }else if(resp==2){
                    	alert_toast("The current highest bid is yours.",'danger')
            			end_load()
                    }
                }
            })
        })
    $('#bid').click(function(){
    	if('<?php echo isset($_SESSION['login_id']) ? 1 : '' ?>' != 1){
    		$('.modal').modal('hide')
    		 uni_modal("LOGIN",'login.php')
    		 return false;
    	}
    	$(this).hide()
    	$('#bid-frm').show()
    })
    $('#cancel_bid').click(function(){
    	$('#bid').show()
    	$('#bid-frm').hide()
    })
</script>
