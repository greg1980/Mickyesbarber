<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #10b981;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #10b981;
            font-size: 28px;
            margin: 0;
        }
        .header p {
            color: #666;
            font-size: 14px;
            margin: 5px 0 0 0;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            margin-right: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-align: center;
        }
        .stat-title {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
        .growth-positive {
            color: #10b981;
        }
        .growth-negative {
            color: #ef4444;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .chart-section {
            margin-bottom: 30px;
        }
        .chart-title {
            font-size: 16px;
            color: #374151;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .data-table th,
        .data-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table th {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .additional-stats {
            display: table;
            width: 100%;
        }
        .stat-item {
            display: table-row;
        }
        .stat-label {
            display: table-cell;
            padding: 8px 0;
            color: #6b7280;
        }
        .stat-data {
            display: table-cell;
            padding: 8px 0;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Generated on {{ $date }}</p>
    </div>

    <!-- Key Metrics -->
    <div class="section">
        <h2 class="section-title">Key Financial Metrics</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value">${{ number_format($stats['totalRevenue'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Monthly Revenue</div>
                <div class="stat-value">${{ number_format($stats['monthlyRevenue'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Daily Revenue</div>
                <div class="stat-value">${{ number_format($stats['dailyRevenue'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Growth Rate</div>
                <div class="stat-value {{ $stats['revenueGrowth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                    {{ $stats['revenueGrowth'] >= 0 ? '+' : '' }}{{ $stats['revenueGrowth'] }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Revenue Data -->
    <div class="section">
        <h2 class="section-title">Monthly Revenue Trend</h2>
        <div class="chart-section">
            <div class="chart-title">Last 12 Months Performance</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($charts['monthlyRevenue']['labels'] as $index => $month)
                    <tr>
                        <td>{{ $month }}</td>
                        <td>${{ number_format($charts['monthlyRevenue']['data'][$index], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Revenue Data -->
    <div class="section">
        <h2 class="section-title">Daily Revenue Analysis</h2>
        <div class="chart-section">
            <div class="chart-title">Last 30 Days Performance</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($charts['dailyRevenue']['labels'], -10) as $index => $day)
                    <tr>
                        <td>{{ $day }}</td>
                        <td>${{ number_format(array_slice($charts['dailyRevenue']['data'], -10)[$index], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <p style="font-size: 12px; color: #6b7280;">Showing last 10 days. Full data available in system.</p>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="section">
        <h2 class="section-title">Booking Statistics</h2>
        <div class="additional-stats">
            <div class="stat-item">
                <div class="stat-label">Total Bookings:</div>
                <div class="stat-data">{{ $additionalStats['totalBookings'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Paid Bookings:</div>
                <div class="stat-data">{{ $additionalStats['paidBookings'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Pending Bookings:</div>
                <div class="stat-data">{{ $additionalStats['pendingBookings'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Failed Bookings:</div>
                <div class="stat-data">{{ $additionalStats['failedBookings'] }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-label">Average Transaction:</div>
                <div class="stat-data">${{ number_format($additionalStats['averageTransaction'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Mickyes Coiffure - Financial Dashboard Report</p>
        <p>This report contains confidential business information</p>
    </div>
</body>
</html>
