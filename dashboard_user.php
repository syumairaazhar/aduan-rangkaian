<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Network Careline</title>
    <link rel="stylesheet" href="style.css"> <!-- External CSS file for styling -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js library for chart -->
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <!-- Sidebar content like menu items -->
        </div>

        <div class="content">
            <h1>Network Careline Dashboard</h1>

            <!-- Quick Summary Cards -->
            <div class="summary-cards">
                <div class="card total-tickets">
                    <h3>Total Tickets Submitted</h3>
                    <p>21</p> <!-- Example data, replace with dynamic value -->
                </div>
                <div class="card pending-tickets">
                    <h3>Pending Tickets</h3>
                    <p>5</p> <!-- Example data, replace with dynamic value -->
                </div>
                <div class="card resolved-tickets">
                    <h3>Resolved Tickets</h3>
                    <p>15</p> <!-- Example data, replace with dynamic value -->
                </div>
                <div class="card urgent-issues">
                    <h3>Urgent Issues</h3>
                    <p>2</p> <!-- Example data, replace with dynamic value -->
                </div>
            </div>

            <!-- Recent Tickets Table -->
            <div class="recent-tickets">
                <h3>Recent Tickets</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Issue Title</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#001</td>
                            <td>Monitor not working</td>
                            <td>Pending</td>
                            <td>2023-05-10</td>
                        </tr>
                        <tr>
                            <td>#002</td>
                            <td>Printer issue</td>
                            <td>Resolved</td>
                            <td>2023-05-09</td>
                        </tr>
                        <!-- Add more rows dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Ticket Status Chart -->
            <div class="ticket-status-chart">
                <h3>Ticket Status Breakdown</h3>
                <canvas id="ticketStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- JavaScript to populate the chart -->
    <script>
        // Example data for ticket status
        const statusData = {
            labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
            datasets: [{
                data: [5, 4, 10, 2], // Example data, replace with dynamic data
                backgroundColor: ['#FF5733', '#F1C40F', '#28B463', '#E74C3C'],
                borderColor: ['#FF5733', '#F1C40F', '#28B463', '#E74C3C'],
                borderWidth: 1
            }]
        };

        const ctx = document.getElementById('ticketStatusChart').getContext('2d');
        const ticketStatusChart = new Chart(ctx, {
            type: 'pie', // You can change this to 'bar' for a bar graph
            data: statusData,
        });
    </script>
</body>
</html>
