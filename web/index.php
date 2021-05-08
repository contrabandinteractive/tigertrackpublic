<!DOCTYPE html >
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tiger Tracker</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Karla&display=swap');
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
       #map {
       	height: 100%;
       	max-width: 1200px;
       	margin: 0 auto 50px;
       	width: 90%;
        border: 4px solid #9684b8;
        border-radius: 10px;
       }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      body {
      	background-image: url('newbg.jpg');
      	background-size: cover;
      	background-attachment: fixed;
      }

      p, h1, h2, h3, h4, div, input, button {
        font-family: 'Karla', sans-serif;
      }

      p {
      	font-size: 24px;
      	font-weight: lighter;
      }

      sup a {
      	color: #3295a8;
      }

      .topContainer {
    	margin: 40px auto;
    	text-align: center;
    	background-color: white;
    	padding: 20px;
    	max-width: 900px;
    	border-radius: 10px;
    }

    .button:hover, input[type="submit"]:hover {
      -webkit-transition: background 0.2s linear;
      -moz-transition: background 0.2s linear;
      -o-transition: background 0.2s linear;
      transition: background 0.2s linear;
      background-color: #9e99a8;
      cursor:pointer;
      }

      .button, input[type="submit"] {
      	background-color: #9684b8;
      	display: block;
      	width: 200px;
      	margin: 20px auto;
      	padding: 20px 10px;
      	font-size: 20px;
        color: #fff;
        font-weight:normal;

      }

      #mapContainer {
        /*
        max-width:1200px;
        width:80%;
        margin:0 auto;
        */
      }

      input {
      	display: block;
      	margin: 20px auto;
      }

      input[type="text"] {
      	padding: 10px;
      	font-size: 20px;
      	border-radius: 5px;
        width: 500px;
        max-width: 90%;
      }

      input[type="submit"] {
      	color: #fff;
      	border: none;
      }

      #alertsSection {
        display: none;
      }

      .checkboxText {
      	font-size: 15px;
      	font-weight: normal;
      }

      #checkBox {
      	margin: 0 auto;
      }

      .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
      }

      /* Modal Content */
      .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
      }

      /* The Close Button */
      .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
      }

      .close:hover,
      .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
      }

      #alertsSection {
      	background-color: #faf7f0;
      	padding: 20px;
      }
    </style>
  </head>

<html>
  <body>

    <div class="topContainer">
      <h1>Welcome to Tiger Tracker</p>
      <p>The biggest threat to the tiger population in Nepal are speeding vehicles. According to the Kathmandu Post, "Every year wild animals die tragically or suffer injuries after being hit by vehicles plying the roads through their natural habitat because of careless drivers and absence of measures required to help wildlife cross busy highways." <sup><a href="https://kathmandupost.com/climate-environment/2021/01/03/death-of-a-tiger-in-a-traffic-accident-highlights-the-threat-to-wildlife" target="_blank">[Source]</a></sup></p>
      <p>Tiger Track aims to solve this problem leveraging the Internet of Things Network. Tigers wearing tracking collars will have their locations monitored by Tiger Track, which will in turn send out SMS text alerts to subscribers warning them when the animals enter specific locations.</p>
      <p>Users can use the map drawing tools below to create custom zones. Once a tiger enters your designated zone, you will receive a text alert.</p>
      <div class="button-container">
        <div class="button" id="getAlerts">Get Alerts</div>
          <div id="alertsSection">
            <form>
              <p>First, use the tools that appear in the map below to draw a circle or rectangle in the region that you'd like to receive alerts.</p>
              <p>The tools look like this:</p>
              <p><img src="tools.jpg" alt="Tools"></p>
              <p>Next, enter your phone number below.</p>
              <input id="phoneField" type="text" name="phoneField" placeholder="Your Phone Number">
              <input id="checkBox" type="checkbox" checked><span class="checkboxText">I consent to receive SMS alerts.</span>
              <input type="submit" value="Submit">
            </form>
          </div>
        <div class="button" id="moreInfo">More Info</div>
      </div>


    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">

      <!-- Modal content -->
      <div class="modal-content">
        <span class="close">&times;</span>
        <p>This project aims to use Azure IoT Hub technology to solve the increasing issue of animals being killed in developing countries as new roads and infrastructure is added. </p>
      </div>

    </div>


      <div id="map"></div>

      <div class="topContainer">
        <p>The map above updates every minute with the latest location of each collared tiger. Each collar is equipped with a device that sends telemetry to the Azure IoT Hub.</p>
      </div>


    <script>
      var customLabel = {
        restaurant: {
          label: 'R'
        },
        bar: {
          label: 'B'
        }
      };

      var markers = [];
      var map;
      var markersArray = [];

        function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: new google.maps.LatLng(27.714730857199953, 85.32836388131095),
          zoom: 12
        });

        const drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.MARKER,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: [
            google.maps.drawing.OverlayType.CIRCLE,
            google.maps.drawing.OverlayType.RECTANGLE,
          ],
        },
        markerOptions: {
          icon:
            "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
        },
        circleOptions: {
          fillColor: "#d6b72b",
          fillOpacity: 1,
          strokeWeight: 5,
          clickable: false,
          editable: true,
          zIndex: 1,
        },
      });
      drawingManager.setMap(map);


        var infoWindow = new google.maps.InfoWindow;


          downloadUrl('get_markers.php', function(data) {
            var xml = data.responseXML;
            markers = xml.documentElement.getElementsByTagName('marker');
            Array.prototype.forEach.call(markers, function(markerElem) {
              var id = markerElem.getAttribute('id');
              var name = markerElem.getAttribute('name');
              var address = markerElem.getAttribute('address');
              var type = markerElem.getAttribute('type');
              var point = new google.maps.LatLng(
                  parseFloat(markerElem.getAttribute('lat')),
                  parseFloat(markerElem.getAttribute('lng')));

              var infowincontent = document.createElement('div');
              var strong = document.createElement('strong');
              strong.textContent = name
              infowincontent.appendChild(strong);
              infowincontent.appendChild(document.createElement('br'));

              var text = document.createElement('text');
              text.textContent = address
              infowincontent.appendChild(text);
              var icon = customLabel[type] || {};
              var marker = new google.maps.Marker({
                map: map,
                position: point,
                label: icon.label,
                icon: 'icon-small.png',
                markerid: icon.label
              });
              marker.addListener('click', function() {
                infoWindow.setContent(infowincontent);
                infoWindow.open(map, marker);
              });
              markersArray.push(marker);
            });
          });


          //Refresh map every few seconds

          setInterval(function(){

            while(markersArray.length) { markersArray.pop().setMap(null); }
            markersArray=[];

            downloadUrl('get_markers.php', function(data) {
              var xml = data.responseXML;
              markers = xml.documentElement.getElementsByTagName('marker');
              Array.prototype.forEach.call(markers, function(markerElem) {
                var id = markerElem.getAttribute('id');
                var name = markerElem.getAttribute('name');
                var address = markerElem.getAttribute('address');
                var type = markerElem.getAttribute('type');
                var point = new google.maps.LatLng(
                    parseFloat(markerElem.getAttribute('lat')),
                    parseFloat(markerElem.getAttribute('lng')));

                var infowincontent = document.createElement('div');
                var strong = document.createElement('strong');
                strong.textContent = name
                infowincontent.appendChild(strong);
                infowincontent.appendChild(document.createElement('br'));

                var text = document.createElement('text');
                text.textContent = address
                infowincontent.appendChild(text);
                var icon = customLabel[type] || {};
                var marker = new google.maps.Marker({
                  map: map,
                  position: point,
                  label: icon.label,
                  icon: 'icon-small.png'
                });
                marker.addListener('click', function() {
                  infoWindow.setContent(infowincontent);
                  infoWindow.open(map, marker);
                });
                markersArray.push(marker);
              });
            });
          }, 30000);




        }



        function downloadUrl(url, callback) {
          var request = window.ActiveXObject ?
              new ActiveXObject('Microsoft.XMLHTTP') :
              new XMLHttpRequest;

          request.onreadystatechange = function() {
            if (request.readyState == 4) {
              request.onreadystatechange = doNothing;
              callback(request, request.status);
            }
          };

          request.open('GET', url, true);
          request.send(null);
        }


      function doNothing() {}
    </script>
    <script async
    src="https://maps.googleapis.com/maps/api/js?key={YOUR GOOGLE API KEY}&callback=initMap&libraries=drawing">
    </script>
    <script>
      $(document).ready(
        function() {

          // Click events
          $('#getAlerts').click(
            function() {
              $('#alertsSection').fadeIn();
            }
          );


          $("form").on("submit", function(event){

              event.preventDefault();


              var formValues= $(this).serialize();

              $.post("process_form.php", formValues, function(data){
                  $("#form-area").fadeToggle();
                  $("#result").html(data);
              });

              $('#alertsSection').html('<p>Thank you, your number has been saved and will receive SMS messages when tigers cross into your selected regions.</p><p style="color:red;font-size:17px;">(NOTE: The production version will use Twilio to send SMS, however, in this demo version you will NOT receive text messages.)</p>');
          });

        }
      );

      // Get the modal
      var modal = document.getElementById("myModal");

      // Get the button that opens the modal
      var btn = document.getElementById("moreInfo");

      // Get the <span> element that closes the modal
      var span = document.getElementsByClassName("close")[0];

      // When the user clicks the button, open the modal
      btn.onclick = function() {
        modal.style.display = "block";
      }

      // When the user clicks on <span> (x), close the modal
      span.onclick = function() {
        modal.style.display = "none";
      }

      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      }
    </script>
  </body>
</html>
