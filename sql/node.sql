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
b_boundary int,
l_boundary int,
r_boundary int,
t_boundary int,
foreign key (b_boundary) references gps(gps_id),
foreign key (l_boundary) references gps(gps_id),
foreign key (r_boundary) references gps(gps_id),
foreign key (t_boundary) references gps(gps_id)
);

-- This is where the gps data actually lies and will be used to determine the approximate location of a user
create table gps (
gps_id int unsigned auto_increment primary key,
boundary int
);

-- Use tinyint for latitude degree
-- Use smallint for longitude degree
-- Use mediumint unsigned for latitude and longitude "minutes"