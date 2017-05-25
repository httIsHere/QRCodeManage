var data = {
				labels : ["January","February","March","April","May","June","July"],
				datasets : [
					{
						lineItemName : "test1",
						fillColor : "rgba(220,220,220,0.5)",
						strokeColor : "rgba(220,220,220,1)",
						pointColor : "rgba(220,220,220,1)",
						pointStrokeColor : "#fff",
						data : [65,59,90,81,56,55,40]
					},
					{
						lineItemName : "test2",
						fillColor : "rgba(151,187,205,0.5)",
						strokeColor : "rgba(151,187,205,1)",
						pointColor : "rgba(151,187,205,1)",
						pointStrokeColor : "#fff",
						data : [28,48,40,19,96,27,100]
					}
				]
			};
			
			
			var chartLine = null;
			window.onload = function(){				
				var ctx = document.getElementById("myChart").getContext("2d");
				chartLine = new Chart(ctx).Line(data);
			}
			