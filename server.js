// filepath: /c:/Users/rog strix/Documents/GitHub/MARIGO-development/fiorentina/server.js
const AWS = require('aws-sdk');
const WebSocket = require('ws');
const path = require('path');
const fs = require('fs');

// Configure AWS SDK with Wasabi credentials
const s3 = new AWS.S3({
    endpoint: new AWS.Endpoint('s3.wasabisys.com'),
    accessKeyId: process.env.WASABI_ACCESS_KEY_ID,
    secretAccessKey: process.env.WASABI_SECRET_ACCESS_KEY,
    region: process.env.WASABI_REGION,
});

const bucketName = process.env.WASABI_BUCKET_NAME;
let filePath = 'chat/messages_bJAT205d.json'; // Default file path

const wss = new WebSocket.Server({ port: 8080 });

wss.on('connection', ws => {
    console.log('Client connected');

    // Function to fetch the file from Wasabi
    const fetchFile = () => {
        const params = {
            Bucket: 'laviola',
            Key: filePath,
        };

        s3.getObject(params, (err, data) => {
            if (err) {
                console.error('Error fetching file from Wasabi:', err);
                return;
            }
            ws.send(data.Body.toString('utf-8'));
        });
    };

    // Fetch the initial file content
    fetchFile();

    // Watch for file changes
    fs.watch(filePath, (eventType, filename) => {
        if (eventType === 'change') {
            fetchFile();
        }
    });

    ws.on('close', () => {
        console.log('Client disconnected');
    });
});

console.log('WebSocket server is running on ws://localhost:8080');