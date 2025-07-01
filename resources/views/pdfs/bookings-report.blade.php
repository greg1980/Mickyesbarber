<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Report - {{ $barber_name }}</title>
    <style>
        @page {
            margin: 40px;
            size: A4;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 20px;
            color: #6B7280;
            margin: 0;
        }

        .report-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: #F9FAFB;
            padding: 15px;
            border-radius: 8px;
        }

        .barber-info h3 {
            margin: 0 0 5px 0;
            color: #374151;
            font-size: 16px;
        }

        .barber-info p {
            margin: 0;
            color: #6B7280;
            font-size: 14px;
        }

        .filters-section {
            background-color: #EEF2FF;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .filters-title {
            font-size: 14px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 10px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .filter-item {
            font-size: 12px;
        }

        .filter-label {
            color: #6B7280;
            display: block;
            margin-bottom: 2px;
        }

        .filter-value {
            color: #374151;
            font-weight: 600;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: #F9FAFB;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #4F46E5;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-section {
            margin-top: 20px;
        }

        .table-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            border-bottom: 2px solid #E5E7EB;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        th {
            background-color: #4F46E5;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #E5E7EB;
        }

        tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        tr:hover {
            background-color: #F3F4F6;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending { background-color: #FEF3C7; color: #92400E; }
        .status-confirmed { background-color: #DBEAFE; color: #1E40AF; }
        .status-completed { background-color: #D1FAE5; color: #065F46; }
        .status-cancelled { background-color: #FEE2E2; color: #991B1B; }
        .status-rescheduled { background-color: #E0E7FF; color: #5B21B6; }

        .payment-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payment-paid { background-color: #D1FAE5; color: #065F46; }
        .payment-unpaid { background-color: #FEE2E2; color: #991B1B; }
        .payment-partially { background-color: #FEF3C7; color: #92400E; }

        .price {
            font-weight: 600;
            color: #059669;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            text-align: center;
            font-size: 11px;
            color: #6B7280;
        }

        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #6B7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Mickyes Coiffure</div>
        <div class="report-title">Bookings Report</div>
    </div>

    <!-- Report Information -->
    <div class="report-info">
        <div class="barber-info">
            <h3>{{ $barber_name }}</h3>
            <p>Generated on {{ $generated_at }}</p>
        </div>
    </div>

    <!-- Applied Filters -->
    <div class="filters-section">
        <div class="filters-title">Applied Filters</div>
        <div class="filters-grid">
            <div class="filter-item">
                <span class="filter-label">Search Term:</span>
                <span class="filter-value">{{ $filters['search'] ?: 'None' }}</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Status:</span>
                <span class="filter-value">{{ $filters['status'] }}</span>
            </div>
            <div class="filter-item">
                <span class="filter-label">Date:</span>
                <span class="filter-value">{{ $filters['date'] }}</span>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats">
        <div class="stat-card">
            <div class="stat-value">{{ $total_bookings }}</div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $total_revenue }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="table-section">
        <div class="table-title">Booking Details</div>

        @if($bookings->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                        <tr>
                            <td>#{{ $booking['id'] }}</td>
                            <td>
                                <strong>{{ $booking['customer_name'] }}</strong><br>
                                <small style="color: #6B7280;">{{ $booking['customer_email'] }}</small>
                            </td>
                            <td>{{ $booking['service_name'] }}</td>
                            <td>{{ $booking['booking_date'] }}</td>
                            <td>{{ $booking['booking_time'] }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($booking['status']) }}">
                                    {{ $booking['status'] }}
                                </span>
                            </td>
                            <td>
                                <span class="payment-badge payment-{{ strtolower(str_replace(' ', '', $booking['payment_status'])) }}">
                                    {{ $booking['payment_status'] }}
                                </span>
                            </td>
                            <td class="price">£{{ $booking['service_price'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-bookings">
                No bookings found matching the selected criteria.
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated automatically by Mickyes Coiffure booking system.</p>
        <p>Newcastle upon Tyne • +44 7852 482489 • info@mickyescoiffure.com</p>
    </div>
</body>
</html>
