# CarWingsImport
A simple PHP script to download your telematics data as a CSV file from the Nissan CarWings servers.

##Usage
Call import.php with the following POST paramters.

username => Your Nissan CarWings Username.

password => Your Nissan CarWings Password.

year => The year you want the data for in the format YYYY.

month => The month you want the data for in the format MM.

##Data
The data is returned as a CSV string with the format: 

Date

Trip ID

Electricity Used (Total) (kWh)

Electricity Used (Consumption) (kWh)

Electricity Used (Generation) (kWh)

Driving Distance (mile)

Energy Economy (miles/kWh)

CO2 Emission Cuts (lb)

