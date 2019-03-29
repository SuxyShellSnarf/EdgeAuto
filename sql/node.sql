create table user (
user_id int unsigned auto_increment primary key
);

create table messages (
message_id int unsigned auto_increment primary key,
message varchar(255),
user_id int unsigned,
foreign key (user_id) references user(user_id)
);

create table vm (
vm_id int unsigned auto_increment primary key,
ip_address tinytext not null,
location_id int unsigned,
foreign key (location_id) references location(location_id)
);

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

create table gps (
gps_id int unsigned auto_increment primary key,
boundary int
);