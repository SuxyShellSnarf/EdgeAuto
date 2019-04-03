-- This creates the user object, this does nothing really but helps with testing
create table user (
user_id int unsigned auto_increment primary key
);

-- This is where the actual CANbus message will be stored on the edge nodes
create table message (
message_id int unsigned auto_increment primary key,
created datetime default CURRENT_TIMESTAMP,
arb_id int unsigned,
message varchar(255),
latitude float,
longitude float,
cantime varchar(255),
user_id int unsigned,
foreign key (user_id) references user(user_id)
);

-- This will act as a directory for vm lookup to switch easily between vms
create table vm (
vm_id int unsigned auto_increment primary key,
ip_address tinytext not null,
location_id int unsigned,
foreign key (location_id) references location(location_id)
);

-- This is the normalized gps boundaries for a vm, this is what vm is referencing
create table location (
location_id int unsigned auto_increment primary key,
x1y1 int unsigned,
x1y2 int unsigned,
x2y1 int unsigned,
x2y2 int unsigned,
foreign key (x1y1) references coordinate(coordinate_id),
foreign key (x1y2) references coordinate(coordinate_id),
foreign key (x2y1) references coordinate(coordinate_id),
foreign key (x2y2) references coordinate(coordinate_id)
);

-- This is where the gps data actually lies and will be used to determine the approximate location of a user
create table coordinate (
coordinate_id int unsigned auto_increment primary key,
x_degree tinyint,
x_minutes mediumint unsigned,
y_degree smallint,
y_minutes mediumint unsigned
);

-- Use tinyint for latitude degree
-- Use smallint for longitude degree
-- Use mediumint unsigned for latitude and longitude "minutes"