import { Server } from 'socket.io';
import { createServer } from 'net';
import { writeFile } from 'fs/promises';

// Function to check if a port is in use
const isPortInUse = (port) => {
    return new Promise((resolve) => {
        const server = createServer()
            .once('error', () => resolve(true))
            .once('listening', () => {
                server.close();
                resolve(false);
            })
            .listen(port);
    });
};

// Function to find next available port
const findAvailablePort = async (startPort) => {
    let port = startPort;
    const maxPort = startPort + 100; // Try up to 100 ports
    while (port < maxPort) {
        if (!(await isPortInUse(port))) {
            return port;
        }
        port++;
    }
    throw new Error('No available ports found in range ' + startPort + ' to ' + maxPort);
};

// Initialize Socket.IO server
const initializeServer = async () => {
    try {
        const port = 3002; // Use fixed port for development
        console.log(`Attempting to start Socket.IO server on port ${port}`);

        const io = new Server(port, {
            cors: {
                origin: [
                    'http://mickyesbarbers.test',
                    'http://localhost:5173',
                    'http://localhost:5174',
                    'http://localhost:5175'
                ],
                methods: ['GET', 'POST', 'OPTIONS'],
                credentials: true,
                allowedHeaders: [
                    'Origin',
                    'X-Requested-With',
                    'Content-Type',
                    'Accept',
                    'Authorization',
                    'X-Socket-ID'
                ]
            },
            path: '/app/local',
            serveClient: false,
            transports: ['websocket'],
            allowEIO3: true,
            pingTimeout: 60000,
            pingInterval: 25000
        });

        // Set up error handling before starting the server
        io.engine.on('error', (error) => {
            console.error('Engine error:', error);
        });

        io.on('connection', (socket) => {
            console.log('Client connected:', socket.id);

            // Handle Pusher protocol events
            socket.on('pusher:subscribe', (data) => {
                console.log('Subscribe request:', data);
                socket.join(data.channel);
                socket.emit('pusher_internal:subscription_succeeded', {
                    channel: data.channel
                });
            });

            socket.on('pusher:unsubscribe', (data) => {
                console.log('Unsubscribe request:', data);
                socket.leave(data.channel);
            });

            socket.on('pusher:ping', () => {
                socket.emit('pusher:pong');
            });

            // Handle client events
            socket.on('client-event', (data) => {
                if (data.channel) {
                    io.to(data.channel).emit(data.event, data.data);
                }
            });

            socket.on('error', (error) => {
                console.error('Socket error:', error);
            });

            socket.on('disconnect', () => {
                console.log('Client disconnected:', socket.id);
            });
        });

        // Save the port number to a file for the client
        await writeFile('.socket-port', port.toString());

        console.log(`Socket.IO server successfully running on port ${port}`);

    } catch (error) {
        console.error('Failed to start Socket.IO server:', error);
        process.exit(1);
    }
};

// Start the server with error handling
initializeServer().catch((error) => {
    console.error('Fatal error starting server:', error);
    process.exit(1);
});
