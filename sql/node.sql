-- This creates the user object, this does nothing really but helps with testing
/*create table user (
user_id int unsigned auto_increment primary key
);*/

-- This is where the actual CANbus message will be stored on the edge nodes
create table message (
message_id int unsigned auto_increment primary key,
created datetime default CURRENT_TIMESTAMP,
arb_id varchar(16),
message varchar(64),
latitude float,
longitude float,
cantime bigint unsigned,
session_id int unsigned
);

-- This is the normalized gps boundaries for a vm, this is what vm is referencing
create table location (
location_id int unsigned auto_increment primary key,
upperlat float,
lowerlat float,
upperlng float,
lowerlng float
);

-- This will act as a directory for vm lookup to switch easily between vms
create table node (
node_id int unsigned auto_increment primary key,
ip_address tinytext not null,
location_id int unsigned unique,
foreign key (location_id) references location(location_id)
);