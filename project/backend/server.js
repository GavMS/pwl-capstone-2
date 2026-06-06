const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');

dotenv.config();

const app = express();
const port = process.env.PORT || 5000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Routes
const authRoutes = require('./routes/auth');
const userRoutes = require('./routes/users');
const roomRoutes = require('./routes/rooms');
const procurementRoutes = require('./routes/procurement');
const assetRoutes = require('./routes/assets');
const consumableRoutes = require('./routes/consumables');
const maintenanceRoutes = require('./routes/maintenance');

app.use('/api/auth',  authRoutes);
app.use('/api/users', userRoutes);
app.use('/api/rooms', roomRoutes);
app.use('/api/procurement', procurementRoutes);
app.use('/api/assets', assetRoutes);
app.use('/api/consumables', consumableRoutes);
app.use('/api/maintenance', maintenanceRoutes);

app.get('/', (req, res) => {
    res.send('Capstone API is running...');
});

app.listen(port, () => {
    console.log(`Backend server running on http://localhost:${port}`);
});
