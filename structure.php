1. Announcement
Announcement_id
Title
Message
PDF_File
Type
Link
Start_date
End_date

2. booking

booking_id
user_id
service_type
vehicle_id
hotel_id
guide_id
event_id
booking_date
booking_time
destination
contact_number
booked_at

3. destination
Destination_id
Name
Description
Location
Photo
Rating
Category


4. event

Event_id
Name
Description
Location
Start_date
End_date
Photo
Destination_id
Ticket_price
Seats_available
Rating
Created_at
availability


5.guide

Guide_id
Name
Phone
Email
Language
Price
Photo
Rating Ascending 1
availability


6. hotel

Hotel_id
Name
Description
Location
Photo
Rating Ascending 1
Price
Rooms_available
Destination_id

7. review

Review_id
User_id
Target_type
Target_id
Rating
Comment
Created_at

8. user

User_id
Name
Email
Password
Role
Photo
Created_at

9. vehicle

vehicle_id
vehicle_name
vehicle_type
vehicle_price
vehicle_availability_number Ascending 1
vehicle_image
vehicle_description
vehicle_location
vehicle_route
created_at