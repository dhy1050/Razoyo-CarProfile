define([
    'jquery',
    'jquery/jquery.cookie'
], function($) {
    "use strict";
    return function () {
        $("#form-search-car").on("submit", function(event) {
            event.preventDefault();
            let searchText = $("#make").val();
            const apiUrl = 'https://exam.razoyo.com/api/cars' + '?make=' + searchText; 

            fetch(apiUrl)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Not response from API.');
                    } else {
                        $.cookie('your-token', response.headers.get('your-token'));
                        return response.json();
                    }
                })
                .then(data => {
                    if (data.cars !== undefined && Object.keys(data.cars).length > 0) {
                        $(".result").html("<p>Search Result: </p>");
                        for (let car of data.cars) {
                            $(".result").append('<div class="car"><p hidden class="id">' + car.id + "</p>"
                                + '<p class="make">Make: ' + car.make + '</p>'
                                + '<p class="model">Model: ' + car.model + '</p>'
                                + '<p calss="year">Year: ' + car.year + '</p>'
                                + '<button class="btn-save-car">Save Car</button>'
                                + '</div>');
                        }
                    } else if (data.makes !== undefined){
                        $(".result").html("<p>Please search make by following:</p>");
                        for (let brand of data.makes) {
                            $(".result").append("<p>" + brand + "</p>" );
                        }
                    }
               })
               .catch(error => console.log(error));
        });

        // Get car data and update
        let updateCarData = function (carId) {
            const url = 'https://exam.razoyo.com/api/cars/' + carId; 
            fetch(url, {
                method: 'GET', 
                headers: new Headers({
                    'Authorization': 'Bearer ' + $.cookie('your-token'), 
                }),
            }).then(response => {
                    if (!response.ok) {
                        throw new Error('Not response from API for get car data');
                    } else {
                        return response.json();
                    }
                })
                .then(data => {
                    $(".car-profile .car-img").attr("src", data.car.image);
                    $(".car-profile .car-year").text("Year: " + data.car.year);
                    $(".car-profile .car-make").text("Make: " + data.car.make);
                    $(".car-profile .car-mode").text("Model: " + data.car.model);
                    $(".car-profile .car-price").text("Price: " + data.car.price);
                    $(".car-profile .car-Searts").text("Seats: " + data.car.seats);
                    $(".car-profile .car-mpg").text("MPG: " + data.car.mpg);
                });
        }

        $(document).on("click", ".btn-save-car", function(event) {
            let id = $(this).parent().find(".id").text();
            let saveCarUrl = "index/saveCar" + "?carid=" + id;
            
            fetch(saveCarUrl).then(response => {
                if (!response.ok) {
                    throw new Error('Not response from Save Car.');
                } else {
                    return response.json();
                }
            }).then(data => {
                updateCarData(data.CarId);
            });
        });
    }
});

