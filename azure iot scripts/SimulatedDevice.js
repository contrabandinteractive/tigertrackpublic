'use strict';
const randomLocation = require('random-location');

// The device connection string to authenticate the device with your IoT hub.
//
// NOTE:
// For simplicity, this sample sets the connection string in code.
// In a production environment, the recommended approach is to use
// an environment variable to make it available to your application
// or use an HSM or an x509 certificate.
// https://docs.microsoft.com/azure/iot-hub/iot-hub-devguide-security
//
// Using the Azure CLI:
// az iot hub device-identity show-connection-string --hub-name {YourIoTHubName} --device-id MyNodeDevice --output table
var connectionString = 'CONNECTION STRING HERE';

// Using the Node.js Device SDK for IoT Hub:
//   https://github.com/Azure/azure-iot-sdk-node
// The sample connects to a device-specific MQTT endpoint on your IoT Hub.
var Mqtt = require('azure-iot-device-mqtt').Mqtt;
var DeviceClient = require('azure-iot-device').Client
var Message = require('azure-iot-device').Message;

var client = DeviceClient.fromConnectionString(connectionString, Mqtt);

function getRandomInRange(from, to, fixed) {
    return (Math.random() * (to - from) + from).toFixed(fixed) * 1;
    // .toFixed() returns string, so ' * 1' is a trick to convert to number
}

// Create a message and send it to the IoT hub every 30 seconds
setInterval(function(){
  // Simulate telemetry.

  //var long = getRandomInRange(-180, 180, 3);
  //var lat = getRandomInRange(-180, 180, 3);

  // For test purposes, generate a random latitude and longitude in a 10km radius of Kathmandu
  // Kathmandu, Nepal 27.70919219099864, 85.3240034902668
  const P = {
    latitude: 27.70919219099864,
    longitude: 85.3240034902668
  }

  // 10km radius from the center of Kathmandu
  const R = 10000 // meters

  let randomPoint = randomLocation.randomCirclePoint(P, R)
  var lat = randomPoint.latitude;
  var long = randomPoint.longitude;


  var message = new Message(JSON.stringify({
    longitude: long,
    latitude: lat
  }));



  console.log('Sending message: ' + message.getData());

  // Send the message.
  client.sendEvent(message, function (err) {
    if (err) {
      console.error('send error: ' + err.toString());
    } else {
      console.log('message sent');
    }
  });
}, 30000);
