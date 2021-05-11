const { EventHubConsumerClient } = require("@azure/event-hubs");

const axios = require('axios');

// If using websockets, uncomment the following require statement
// const WebSocket = require("ws");

// If you need proxy support, uncomment the below code to create proxy agent
// const HttpsProxyAgent = require("https-proxy-agent");
// const proxyAgent = new HttpsProxyAgent(proxyInfo);

// Event Hub-compatible endpoint
// az iot hub show --query properties.eventHubEndpoints.events.endpoint --name {your IoT Hub name}
const eventHubsCompatibleEndpoint = "END POINT";

// Event Hub-compatible name
// az iot hub show --query properties.eventHubEndpoints.events.path --name {your IoT Hub name}
const eventHubsCompatiblePath = "tigertrack";

// Primary key for the "service" policy to read messages
// az iot hub policy show --name service --query primaryKey --hub-name {your IoT Hub name}
const iotHubSasKey = "PRIMARY KEY";

// If you have access to the Event Hub-compatible connection string from the Azure portal, then
// you can skip the Azure CLI commands above, and assign the connection string directly here.
const connectionString = `Endpoint=${eventHubsCompatibleEndpoint};EntityPath=${eventHubsCompatiblePath};SharedAccessKeyName=service;SharedAccessKey=${iotHubSasKey}`;

var printError = function (err) {
  console.log(err.message);
};

// Display the message content - telemetry and properties.
// - Telemetry is sent in the message body

var printMessages = function (messages) {
  for (const message of messages) {
    console.log("Telemetry received: ");
    console.log(JSON.stringify(message.body));
    console.log("Properties (set by device): ");
    console.log(JSON.stringify(message.properties));
    console.log("System properties (set by IoT Hub): ");
    console.log(JSON.stringify(message.systemProperties));
    console.log("");


    // Send location to server
    axios.get('https://contrabandinteractive.com/tigertrack/update_location.php?id=' + message.body.tigerid + '&latitude=' + message.body.latitude + '&longitude=' + message.body.longitude).then(resp => {

        console.log(resp.data);
    });

  }
};

async function main() {
  console.log("Tiger Tracker - Read Collars and post location to server.");


  const clientOptions = {
    // webSocketOptions: {
    //   webSocket: WebSocket,
    //   webSocketConstructorOptions: {}
    // }
  };

  // Create the client to connect to the default consumer group of the Event Hub
  const consumerClient = new EventHubConsumerClient("$Default", connectionString, clientOptions);

  // Subscribe to messages from all partitions as below
  // To subscribe to messages from a single partition, use the overload of the same method.
  consumerClient.subscribe({
    processEvents: printMessages,
    processError: printError,
  });
}

main().catch((error) => {
  console.error("Error running sample:", error);
});
