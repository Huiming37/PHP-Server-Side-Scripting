
<?php if(isset($_GET["keyword"])): ?>

<?php 
		
		$keyword  = $_GET["keyword"];
		$type 	  = $_GET["type"];

		if($_GET["distance"]==""){
			$distance = 16090;
		}else{
			$distance = (int)$_GET["distance"]*1609 ;
		}

		if(isset($_GET["address"])){
			 $url='https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($_GET["address"]) .
			 '&key=AIzaSyBjuUbBfIUxkhziXF2nihPXTgG5bEaXoU8';

			 $result = file_get_contents($url);

			 $jsonObj = (json_decode($result));

			 if (array_key_exists("geometry",$jsonObj -> results[0])){

			 	$lat = (string)($jsonObj  -> results[0] -> geometry -> location -> lat);
				$lng = (string)($jsonObj -> results[0] -> geometry -> location -> lng);

				$url='https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $lat . "," . $lng . "&radius=" . $distance . '&type=' .  urlencode($type) . '&keyword='. urlencode($keyword) . '&key=AIzaSyBjuUbBfIUxkhziXF2nihPXTgG5bEaXoU8';

				$result = file_get_contents($url);

				$jsonObj = json_decode($result);

				$startLocation = array("lat" => $lat, "lng" => $lng);

				$jsonObj -> startLocation = $startLocation;

				$result = json_encode($jsonObj);

				echo $result;

			}else {

				echo $result;
			}

		}else {

			$lat = $_GET["lat"];
			$lng = $_GET["lng"];

			$url='https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=' . $lat . "," . $lng . "&radius=" . $distance . '&type=' .  urlencode($type) . '&keyword='. urlencode($keyword) . '&key=AIzaSyBjuUbBfIUxkhziXF2nihPXTgG5bEaXoU8';

			$result = file_get_contents($url);

			$jsonObj = json_decode($result);

			$startLocation = array("lat" => $lat, "lng" => $lng);

			$jsonObj -> startLocation = $startLocation;

			$result = json_encode($jsonObj);

			echo $result;
		}

		

 ?>

<?php elseif (isset($_GET["place_id"])): ?>

	<?php 

		$place_id  = $_GET["place_id"];

		$url='https://maps.googleapis.com/maps/api/place/details/json?placeid=' . $place_id . '&key=AIzaSyBjuUbBfIUxkhziXF2nihPXTgG5bEaXoU8';

		$result = file_get_contents($url);

		echo $result;

		$detailObj = (json_decode($result));

		if (array_key_exists("photos",$detailObj -> result)){

			$photos = $detailObj -> result -> photos;

			for($i = 0; $i < 5 & $i < sizeof($photos); $i++){

				$url = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth=750&photoreference='.$photos[$i] -> photo_reference.'&key=AIzaSyBjuUbBfIUxkhziXF2nihPXTgG5bEaXoU8';

				$result = file_get_contents($url);

				file_put_contents($i.".png",$result);
			}
		}
	
	 ?> 

<?php else : ?>

<!DOCTYPE html>
<html>
<head>
	
	<title>Travel and Entertainment Search”</title>
	<style type="text/css">
	#displayForm{
		width: 600px;
		height:220px;
		margin:auto;
		border:2px solid rgb(143, 145, 147);
		background-color: rgb(247, 250, 255);
	}

	h1{
		margin:auto;
		font-weight:300;
		font-style:italic;
		text-align: center; 
	}

	hr{
		width:580px;
		border:0.5px solid #545352;
	}

	label{
		font-weight:bold;
		margin-left: 5px;
	}

	input{
		margin-top: 5px;
	}

	.firstPart{
		display:block;
		float: left;
	}
	
	.secondPart{
		display:inline-block;
		margin-top: 3px;
		margin-left: 6px;
	}

	#table{
		margin-top: 20px;
		text-align:center;
	}

	a{
		text-decoration: none;
		color:black;
	}

	.map{
		position: absolute;
		height: 250px;
		width: 250px;
	}

	.method{
    background-color: rgb(202, 204, 206);
    width:80px;
    height:30px;
    font-size: 15px;
    text-align:center;
    line-height:30px; 
  }

  .method:hover{
    background-color:rgb(124, 127, 130);
  }

  .floating-panel{
  	position:absolute;
  	z-index: 5;
  	overflow: hidden; 
  }

  td p{
  	margin-top: 5px;
  	margin-bottom: 5px;
  }

  #reviewDetails tr{
  	height:22px;
  }


	</style>
	
</head>
<body onload="init()">

	<div id="displayForm">
		<h1>Travel and Entertainment Search</h1>
		<hr>
		<form method="POST" id="form" name="form" action="index.php">
			<label>Keyword</label>
			<input type="text" name="keyword" id="keyword" required="required"><br>
			<label>Category</label>
			<select style="margin-top: 5px;" name = "type" id="type">
				<option value="default">default</option>
				<option value="cafe">cafe</option>
				<option value="bakery">bakery</option>
				<option value="restaurant">restaurant</option>
				<option value="beauty_salon">beauty salon</option>
				<option value="casino">casino</option>
				<option value="movie_theater">movie theater</option>
				<option value="lodging">lodging</option>
				<option value="airport">airport</option>
				<option value="train_station">train station</option>
				<option value="subway_station">subway station</option>
				<option value="bus_station">bus station</option>
			</select>

			<br>
			
			<div class = "firstPart">			
				<label >Distance(miles)</label>
				<input type="text" name="distance" id = "distance" placeholder="10">
				<label>from</label>
			</div>

			<div class = "secondPart">
				<input type="radio" name="startLocation" id = "Here" value = "Here" checked = "checked" onclick="disableInput()">Here <br>

				<input type="radio" name="startLocation" id = "There" value = "There" onclick="enableInput()">
				<input type="text"  id="locationInput" name="locationInput" disabled="diabled" placeholder="location" required="required" style="width: 152px" >
			</div>
				
			<br>
			<br>

			<button type="button" name="submit" id="searchButton" onclick="isValidForm()" disabled="disabled" style="margin-left: 70px" >Search</button>
			<button type="button" name="clear" onclick= "clearForm()" >Clear</button>
			<button type="submit" id="submitButton" hidden>Clear</button>
		</form>
	</div>

	<div id="table" ></div>

</body>


 <script type="text/javascript">

 		var localLat;
 		var localLng;
 		var startLat;
 		var startLng;
 		var mode;

 		function isSetMap(coordinates){

 			var count = 0;
 			var forthComma;

 			for(var i=0;i<coordinates.length;i++)
			{
				if(coordinates.charAt(i)== ","){
					count++;
					if(count == 4){
						forthComma = i;
					}
				}
			}

			var index=coordinates.substring(forthComma+1,coordinates.length);
			var elementMap = document.getElementsByClassName('googleMap')[index];

			if(elementMap.hasAttribute('hidden')){
				elementMap.removeAttribute('hidden');
				initMap(coordinates);
			}else{
				elementMap.setAttribute('hidden', true);
			}

		}

      function initMap(coordinates) {

      	console.log(coordinates);

 		var directionsDisplay = new google.maps.DirectionsRenderer;
        var directionsService = new google.maps.DirectionsService;

      	var commaIndexs = new Array(4);
      	var count = 0;
      	for(var i=0;i<coordinates.length;i++)
		{
			if(coordinates.charAt(i)== ","){
				commaIndexs[count] = i;
				count++;
			}
		}

		var startLat=coordinates.substring(0,commaIndexs[0]);
      	var startLng=coordinates.substring(commaIndexs[0]+1,commaIndexs[1]);
      	var destinationLat=coordinates.substring(commaIndexs[1]+1,commaIndexs[2]);
      	var destinationLng=coordinates.substring(commaIndexs[2]+1,commaIndexs[3]);
      	var index=coordinates.substring(commaIndexs[3]+1,coordinates.length);

        var startLocation = {lat: parseFloat(startLat) , lng: parseFloat(startLng) };
        var destinationLocation = {lat: parseFloat(destinationLat) , lng: parseFloat(destinationLng) };
        
        var map = new google.maps.Map(document.getElementsByClassName('map')[index], {
          zoom: 15,
          center: destinationLocation
        });

        var marker = new google.maps.Marker({
          position: destinationLocation,
          map: map
        });

        directionsDisplay.setMap(map);

        var floatingPanels = document.getElementsByClassName('floating-panel');

		for(i=0;i<floatingPanels.length;i++){
			floatingPanels[i]. addEventListener('click', function() {
	          calculateAndDisplayRoute(directionsService, directionsDisplay,startLocation,destinationLocation, mode );
	          marker.setVisible(false);
	        });
		}

    }

	 function calculateAndDisplayRoute(directionsService, directionsDisplay,startLocation,destinationLocation , mode) {
     
        directionsService.route({
          origin: startLocation,
          destination: destinationLocation,  
          travelMode: mode
        }, function(response, status) {
          if (status == 'OK') {
            directionsDisplay.setDirections(response);
          } else {
            window.alert('Directions request failed due to ' + status);
          }
        });
      }

		function init(){
  
			var URL = "http://ip-api.com/json";

			if (window.XMLHttpRequest)
	      	{// code for IE7+, Firefox, Chrome, Opera, Safari
	       		xmlhttp=new XMLHttpRequest();
	   		} else {// code for IE6, IE5
	    		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");  
	    	}

			xmlhttp.open("GET",URL,false); 
			xmlhttp.send();	
			jsonObj= JSON.parse(xmlhttp.responseText);

			document.getElementById("searchButton").removeAttribute('disabled');

			localLat=jsonObj.lat.toString();
			localLng=jsonObj.lon.toString();
			
		}


		function isValidForm(){

			var form = document.getElementById("form");
			var submitButton = document.getElementById("submitButton");
			if(!form.checkValidity()){
				submitButton.click();
			}else{
				submitForm(); 
			}

		}

		function submitForm() {

			var URL = "";
			var keyword = document.getElementById("keyword").value;
			var type = document.getElementById("type").value;
			var distance = document.getElementById("distance").value;
			var radioHere = document.getElementById("Here");
			if(radioHere.checked == true){
				var lat = localLat;
				var lng = localLng;
				URL="index.php?keyword="+keyword+"&type="+type+"&lat="+lat+"&lng="+lng +"&distance="+distance;
			}else{
				var address = document.getElementById("locationInput").value;
				URL="index.php?keyword="+keyword+"&type="+type+"&address="+address+"&distance="+distance;
			}


			if (window.XMLHttpRequest)
	      	{// code for IE7+, Firefox, Chrome, Opera, Safari
	       		xmlhttp=new XMLHttpRequest();
	   		} else {// code for IE6, IE5
	    		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");  
	    	}

			xmlhttp.open("GET",URL,false); 

			xmlhttp.send();	

			var jsonObj = JSON.parse(xmlhttp.responseText);

			console.log(jsonObj);

			if(jsonObj.results.length == 0){

				document.getElementById("table").innerHTML = "<div style = 'border: 2px solid rgb(143, 145, 147); width: 700px; margin:auto' ><b>No Records Has Been Found</b></div>";
			}else{

				startLat = jsonObj.startLocation.lat;
				startLng = jsonObj.startLocation.lng;
				document.getElementById("table").innerHTML = generateTable(jsonObj);
			}
			
		}

		function generateTable(placeObj){

			var places = placeObj;

			var html_text="<table border='2' style='margin:auto'>"; 

		   	html_text+="<thead style='text-align: center' >"; 

		   	html_text+="<tr>";

	    	html_text+="<td ><b>";
	    	html_text+="Category";
	    	html_text+="</b></td>";	
	    	html_text+="<td ><b>";
	    	html_text+="Name";
	    	html_text+="</b></td>";	
	    	html_text+="<td ><b>";
	    	html_text+="Address";
	    	html_text+="</td>";	
		   
		    html_text+="</tr>";
		  	html_text+="<thead>"; 

		   	html_text+="<tbody>"; 

		   	for(var i=0; i<places.results.length; i++){
		   		html_text+="<tr>";

		   		html_text+="<td>";
				html_text+= "<img src=";
				html_text+= places.results[i].icon ;
				html_text+= " width=30px, height=25px>";
		   		html_text+="</td>";

		   		html_text+="<td onclick = searchDetail('";
		   		html_text+=(places.results[i].place_id).toString();
		   		html_text+="') style='cursor:pointer' >";
		   		html_text+= places.results[i].name;
		   		html_text+="</td>";


		   		html_text+="<td>";
		   		html_text+="<p onclick = isSetMap('";
		   		html_text+=startLat;
		   		html_text+=",";
		   		html_text+=startLng;
		   		html_text+=",";
		   		html_text+=(places.results[i].geometry.location.lat).toString();
		   		html_text+=",";
		   		html_text+=(places.results[i].geometry.location.lng).toString();
		   		html_text+=",";
		   		html_text+=i;
		   		html_text+="') style='cursor:pointer'>";

		   		html_text+=places.results[i].vicinity;
		   		html_text+="</p>"
		   		html_text+="<div class ='googleMap' hidden>";
		   		html_text+="<div class='floating-panel'>"
		   		html_text+="<div class='method' style='cursor:pointer' onclick = changeMode('WALKING')>Walk there</div>";
		   		html_text+="<div class='method' style='cursor:pointer' onclick = changeMode('BICYCLING')>Bike there</div>";
		   		html_text+="<div class='method' style='cursor:pointer' onclick = changeMode('DRIVING')>Drive there</div>";
		   		html_text+="</div>"
		   		html_text+="<div class ='map'></div>"

		   		html_text+="</td>";

		   		html_text+="</tr>";
		   	}

			html_text+="</tbody>"; 
			html_text+="</table>"; 

			return html_text;

		}

		function searchDetail(id){

			console.log(id)

			var URL= "index.php?place_id=" + id;
			
			if (window.XMLHttpRequest)
	      	{// code for IE7+, Firefox, Chrome, Opera, Safari
	       		xmlhttp=new XMLHttpRequest();
	   		} else {// code for IE6, IE5
	    		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");  
	    	}

	    	xmlhttp.open("GET",URL,false); 

			xmlhttp.send();	

			detailObj= JSON.parse(xmlhttp.responseText);

			document.getElementById("table").innerHTML = generateDetailForm(detailObj);
			
		}

		function generateDetailForm(detailObj){

		
			var detail_text= "<div><b>";
			detail_text+= detailObj.result.name;
	   		detail_text+="</b></div>";

	   		detail_text+= "<div id='clickReview' style='margin-top:20px' >";
			detail_text+= "click to show reviews";
	   		detail_text+="</div>";

	   		detail_text+= "<div style='margin-top:10px' >";
	   		detail_text+= "<img id='reviewArrow' src ='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' onclick='showReviews()' width=30px,height=20px >";
	   		detail_text+= "</div>";

	   		detail_text+= "<div id ='reviewDetails' hidden>";

			detail_text+="<table border='1px' width=700px style='margin:auto' >"; 

		   	detail_text+="<tbody>"; 

	   		if("reviews" in detailObj.result){
				var reviews = detailObj.result.reviews;
			   	for(var i=0; i<5 && i < reviews.length; i++){
			   		detail_text+="<tr>";
			   		detail_text+="<td>";
					detail_text+= "<img src=";
					detail_text+= reviews[i].profile_photo_url;
					detail_text+= " width=20px, height=20px ><b>";
					detail_text+= reviews[i].author_name;
			   		detail_text+="</b></td>";
			   		detail_text+="</tr>";

			   		detail_text+="<tr>";
			   		detail_text+="<td>";
					detail_text+= reviews[i].text;
			   		detail_text+="</td>";
			   		detail_text+="</tr>";
			   	}
			}else{
				detail_text+="<tr>";
		   		detail_text+="<td><b>";
				detail_text+= "No Reviews Found";
		   		detail_text+="</b></td>";
		   		detail_text+="</tr>";
			}


			detail_text+="</tbody>"; 
			detail_text+="</table>"; 

	   		detail_text+="</div>";

	   		detail_text+= "<div id='clickPhoto'  style='margin-top:20px' >";
			detail_text+= "click to show photos";
	   		detail_text+="</div>";

	   		detail_text+= "<div style='margin-top:10px'>";
	   		detail_text+= "<img id='photoArrow' src= 'http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' onclick=showPhotos() width=30px,height=20px>";
	   		detail_text+= "</div>";


	   		detail_text+= "<div id ='photoDetails' width=700px hidden>";
	   		detail_text+="<table border='1px' width=200px style='margin:auto' >"; 
		   	detail_text+="<tbody>"; 

		   	if("photos" in detailObj.result){
				var photos = detailObj.result.photos;
			 	for(var i=0; i < 5 && i < photos.length; i++){
			   		detail_text+="<tr>";
			   		detail_text+="<td>";
			   		detail_text+= "<a href="
			   		detail_text+= i;
			   		detail_text+=".png?a="
			   		detail_text+= photos[i].photo_reference; 
			   		detail_text+=" target=_blank>"
					detail_text+= "<img src=";
					detail_text+= i;
					detail_text+=".png?a="
					detail_text+= photos[i].photo_reference; 
			   		detail_text+="></a></td>";
			   		detail_text+="</tr>";
		   		}

				}else{

			   		detail_text+="<tr>";
			   		detail_text+="<td><b>";
					detail_text+= "No Photos Found";
			   		detail_text+="</b></td>";
			   		detail_text+="</tr>";
				}

			detail_text+="</tbody>"; 
			detail_text+="</table>"; 

	   		detail_text+= "</div>";

	   		return detail_text;

		}

		function changeMode(method){
			mode = method;
		}

		function showReviews(){

			var clickReview = document.getElementById("clickReview");
			var clickPhoto = document.getElementById("clickPhoto");

			var reviewsDetail = document.getElementById("reviewDetails");
			var photosDetail = document.getElementById("photoDetails");
			var photoImg = document.getElementById("photoArrow");
			var reviewImg = document.getElementById("reviewArrow");

			if(reviewsDetail.hasAttribute('hidden')){
				reviewsDetail.removeAttribute('hidden');
				clickReview.innerHTML = "click to hide reviews";
			}else{
				reviewsDetail.setAttribute('hidden', true);
				clickReview.innerHTML = "click to show reviews";
			}

			console.log(reviewImg.src);
			if(reviewImg.src == "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png"){
				reviewImg.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png"	
			}else{
				reviewImg.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png"
			}

			if(!photosDetail.hasAttribute('hidden')){
				photosDetail.setAttribute('hidden', true);
				photoImg.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
				clickPhoto.innerHTML = "click to show photos";
			}


		}

		function showPhotos(){

			var clickReview = document.getElementById("clickReview");
			var clickPhoto = document.getElementById("clickPhoto");

			var reviewsDetail = document.getElementById("reviewDetails");
			var photosDetail = document.getElementById("photoDetails");
			var photoImg = document.getElementById("photoArrow");
			var reviewImg = document.getElementById("reviewArrow");

			if(photosDetail.hasAttribute('hidden')){
				photosDetail.removeAttribute('hidden');
				clickPhoto.innerHTML = "click to hide photos";
			}else{
				photosDetail.setAttribute('hidden', true);
				clickPhoto.innerHTML = "click to show photos";
			}

			if(photoImg.src == "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png"){
				photoImg.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png"	
			}else{
				photoImg.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png"
			}

			if(!reviewsDetail.hasAttribute('hidden')){
				reviewsDetail.setAttribute('hidden', true);
				reviewImg.src = "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png";
				clickReview.innerHTML = "click to show reviews";
			}

		}

		function clearForm() {
			document.getElementById("keyword").value = "";
			document.getElementById("distance").value = "";
			document.getElementById("type").value = "default";
			document.getElementById("Here").checked = true;
			document.getElementById("table").innerHTML ="";
			document.getElementById("locationInput").value ="";
			disableInput();
		}

		function enableInput() {
			var inputLocation = document.getElementById("locationInput");
			inputLocation.removeAttribute('disabled');
		}

		function disableInput() {
			var inputLocation = document.getElementById("locationInput");
			inputLocation.disabled=true;
		}

	</script>

	<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDyL7Lqir9RtxOtltgp72-tNdSP4m11NrU">
    </script> 

</html>

<?php endif; ?>