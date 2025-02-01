const AWS = require('aws-sdk');
const WebSocket = require('ws');
const https = require('https');
const fs = require('fs');

// Configure AWS SDK with Wasabi credentials
const s3 = new AWS.S3({
    endpoint: new AWS.Endpoint('s3.wasabisys.com'),
    accessKeyId: process.env.WASABI_ACCESS_KEY_ID,
    secretAccessKey: process.env.WASABI_SECRET_ACCESS_KEY,
    region: process.env.WASABI_REGION,
});

const bucketName = process.env.WASABI_BUCKET_NAME;


const ws = new WebSocket.Server({ server });

ws.on('connection', ws => {
    console.log('Client connected');

    let filePath = null;

    // Function to fetch the file from Wasabi
    const fetchFile = () => {
        if (!filePath) {
            console.error('File path is not set.');
            return;
        }

        const params = {
            Bucket: bucketName,
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

    // Handle incoming messages from the client
    ws.on('message', message => {
        const data = JSON.parse(message);
        if (data.type === 'setFilePath') {
            filePath = data.filePath;
            console.log('File path set to:', filePath);
            fetchFile();
        }
    });

    ws.on('close', () => {
        console.log('Client disconnected');
    });
});

server.listen(8080, () => {
    console.log('WebSocket server is running on ws://localhost:8080');
});