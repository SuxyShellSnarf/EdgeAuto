create table session (
session_id int unsigned auto_increment primary key
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