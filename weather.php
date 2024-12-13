

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#getWeather").on("click", function () {
                var zipCode = $("#zipCode").val();
                if (zipCode === "") {
                    alert("Please enter a pincode.");
                    return;
                }

                var apiKey = "f7e6e9ddee0a561107164b079620aeb9";  // OpenWeatherMap API Key
                var url = `https://api.openweathermap.org/data/2.5/weather?zip=${zipCode},ca&appid=${apiKey}&units=metric`;

                $.ajax({
                    url: url,
                    method: "GET",
                    success: function (data) {
                        $("#weatherResult").html(`
                            <h2 class="text-xl font-semibold text-blue-600">Weather for ${data.name}</h2>
                            <p class="text-gray-800">Temperature: ${data.main.temp} °C</p>
                            <p class="text-gray-800">Weather: ${data.weather[0].description}</p>
                            <p class="text-gray-800">Humidity: ${data.main.humidity}%</p>
                            <p class="text-gray-800">Wind Speed: ${data.wind.speed} m/s</p>
                        `);
                    },
                    error: function (err) {
                        $("#weatherResult").html(`
                            <p class="text-red-500">Error: Unable to fetch weather data. Please check the pincode or try again later.</p>
                        `);
                    }
                });
            });
        });
    </script>
</head>

<body class="bg-blue-50 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-96">
        <h1 class="text-2xl font-bold text-center text-blue-600">Weather Finder</h1>
        <p class="text-center text-gray-600 mt-2">Enter a Pincode to get the weather information.</p>
        
        <div class="mt-6">
            <input type="text" id="zipCode" class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter Pincode">
            <button id="getWeather" class="w-full bg-blue-600 text-white py-2 rounded-md mt-4 hover:bg-blue-700">Get Weather</button>
        </div>

        <div id="weatherResult" class="mt-6 text-center text-gray-800"></div>
        <a href="dashboard_user.php">Go back to Dashboard</a>.
    </div>
</body>

</html>
