<?php
class HbDataBaseSchema {

	private $charset_collate;

	public function __construct( $hbdb ) {
		global $wpdb;
		$this->hbdb = $hbdb;
		$this->charset_collate = '';
		if ( ! empty( $wpdb->charset ) ) {
			$this->charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$this->charset_collate .= " COLLATE {$wpdb->collate}";
		}
	}

	public function get_schema() {
		return
"
CREATE TABLE {$this->hbdb->resa_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
check_in date NOT NULL,
check_out date NOT NULL,
accom_id bigint(20) NOT NULL,
accom_num bigint(20) NOT NULL,
adults smallint(10) NOT NULL,
children smallint(10) NOT NULL,
accom_price decimal(14,2) NOT NULL,
discount varchar(200) NOT NULL,
price decimal(14,2) NOT NULL,
previous_price decimal(14,2) NOT NULL,
deposit decimal(14,2) NOT NULL,
paid decimal(14,2) NOT NULL,
payment_gateway varchar(30) NOT NULL,
currency varchar(3) NOT NULL,
customer_id bigint(20) NOT NULL,
status varchar(20) NOT NULL,
options text NOT NULL,
fees text NOT NULL,
additional_info text NOT NULL,
payment_type varchar(30) NOT NULL,
payment_info text NOT NULL,
admin_comment text NOT NULL,
lang varchar(20) NOT NULL,
coupon varchar(200) NOT NULL,
coupon_value decimal(14,2) NOT NULL,
payment_token varchar(40) NOT NULL,
payment_status varchar(40) NOT NULL,
payment_status_reason varchar(40) NOT NULL,
amount_to_pay decimal(14,2) NOT NULL,
received_on DATETIME NOT NULL,
updated_on DATETIME NOT NULL,
uid varchar(256) NOT NULL,
alphanum_id varchar(8) NOT NULL,
invoice_counter bigint(20) NOT NULL,
origin varchar(40) NOT NULL,
origin_url varchar(512) NOT NULL,
synchro_id varchar(128) NOT NULL,
booking_form_num tinyint(4) NOT NULL,
parent_id bigint(20) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->parents_resa_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
price decimal(14,2) NOT NULL,
previous_price decimal(14,2) NOT NULL,
deposit decimal(14,2) NOT NULL,
paid decimal(14,2) NOT NULL,
payment_gateway varchar(30) NOT NULL,
currency varchar(3) NOT NULL,
customer_id bigint(20) NOT NULL,
options text NOT NULL,
fees text NOT NULL,
additional_info text NOT NULL,
payment_type varchar(30) NOT NULL,
payment_info text NOT NULL,
admin_comment text NOT NULL,
lang varchar(20) NOT NULL,
payment_token varchar(40) NOT NULL,
payment_status varchar(40) NOT NULL,
payment_status_reason varchar(40) NOT NULL,
amount_to_pay decimal(14,2) NOT NULL,
received_on DATETIME NOT NULL,
alphanum_id varchar(8) NOT NULL,
invoice_counter bigint(20) NOT NULL,
origin varchar(40) NOT NULL,
booking_form_num tinyint(4) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->customers_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
email varchar(127) NOT NULL,
info text NOT NULL,
payment_id text NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->rates_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
type varchar(15) NOT NULL,
all_accom tinyint(1) NOT NULL,
all_seasons tinyint(1) NOT NULL,
amount decimal(14,2) NOT NULL,
nights smallint(6) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->rates_accom_table} (
rate_id bigint(20) NOT NULL,
accom_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->rates_seasons_table} (
rate_id bigint(20) NOT NULL,
season_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->rates_rules_table} (
rate_id bigint(20) NOT NULL,
rule_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->discounts_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
apply_to_type varchar(10) NOT NULL,
amount decimal(14,2) NOT NULL,
amount_type varchar(10) NOT NULL,
all_accom tinyint(1) NOT NULL,
all_seasons tinyint(1) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->discounts_accom_table} (
discount_id bigint(20) NOT NULL,
accom_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->discounts_rules_table} (
discount_id bigint(20) NOT NULL,
rule_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->coupons_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
code varchar(50) NOT NULL,
amount decimal(14,2) NOT NULL,
amount_type varchar(10) NOT NULL,
all_accom tinyint(1) NOT NULL,
all_seasons tinyint(1) NOT NULL,
use_count bigint(20) NOT NULL,
max_use_count bigint(20) NOT NULL,
date_limit date NOT NULL,
multiple_use_per_customer varchar(10) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->coupons_accom_table} (
coupon_id bigint(20) NOT NULL,
accom_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->coupons_rules_table} (
coupon_id bigint(20) NOT NULL,
rule_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->coupons_seasons_table} (
coupon_id bigint(20) NOT NULL,
season_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->seasons_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(200) NOT NULL,
priority varchar(15) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->seasons_dates_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
season_id bigint(20) NOT NULL,
start_date date NOT NULL,
end_date date NOT NULL,
days varchar(13) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->fields_table} (
id varchar(50) NOT NULL,
name varchar(200) NOT NULL,
standard tinyint(1) NOT NULL,
displayed tinyint(1) NOT NULL,
required tinyint(1) NOT NULL,
admin_only tinyint(1) NOT NULL,
type varchar(20) NOT NULL,
has_choices tinyint(1) NOT NULL,
order_num bigint(20) NOT NULL,
data_about varchar(15) NOT NULL,
column_width varchar(15) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->fields_choices_table} (
id varchar(50) NOT NULL,
field_id varchar(50) NOT NULL,
name varchar(200) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->strings_table} (
id varchar(60) NOT NULL,
locale varchar(20) NOT NULL,
value varchar(1000) NOT NULL,
PRIMARY KEY  (id, locale)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->booking_rules_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
type varchar(20) NOT NULL,
name varchar(50) NOT NULL,
check_in_days varchar(13) NOT NULL,
check_out_days varchar(13) NOT NULL,
minimum_stay smallint(6) NOT NULL,
maximum_stay smallint(6) NOT NULL,
all_accom tinyint(1) NOT NULL,
all_seasons tinyint(1) NOT NULL,
conditional_type varchar(20) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->booking_rules_accom_table} (
rule_id bigint(20) NOT NULL,
accom_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->options_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(200) NOT NULL,
amount decimal(14,2) NOT NULL,
amount_children decimal(14,2) NOT NULL,
choice_type varchar(8) NOT NULL,
link varchar(25) NOT NULL,
apply_to_type varchar(25) DEFAULT NULL,
quantity_max_option varchar(25) NOT NULL,
quantity_max int(11) NOT NULL,
quantity_max_child int(11) NOT NULL,
all_accom tinyint(1) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->options_choices_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
option_id bigint(20) NOT NULL,
name varchar(200) NOT NULL,
amount decimal(14,2) NOT NULL,
amount_children decimal(14,2) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->options_accom_table} (
	option_id bigint(20) NOT NULL,
	accom_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->fees_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(200) NOT NULL,
amount decimal(14,2) NOT NULL,
amount_children decimal(14,2) NOT NULL,
apply_to_type varchar(50) DEFAULT NULL,
all_accom tinyint(1) NOT NULL,
global tinyint(1) NOT NULL,
accom_price_per_person_per_night tinyint(1) NOT NULL,
include_in_price tinyint(1) NOT NULL,
minimum_amount decimal(14,2) NOT NULL,
maximum_amount decimal(14,2) NOT NULL,
multiply_per varchar(25) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->fees_accom_table} (
fee_id bigint(20) NOT NULL,
accom_id bigint(20) NOT NULL,
PRIMARY KEY  (fee_id, accom_id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->accom_num_name_table} (
accom_id bigint(20) NOT NULL,
accom_num bigint(20) NOT NULL,
num_name varchar(50) DEFAULT NULL,
PRIMARY KEY  (accom_id,accom_num)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->email_templates_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(200) NOT NULL,
to_address varchar(200) NOT NULL,
reply_to_address varchar(200) NOT NULL,
from_address varchar(200) NOT NULL,
bcc_address varchar(200) NOT NULL,
subject varchar(200) NOT NULL,
message longtext NOT NULL,
format varchar(10) NOT NULL,
media_attachments varchar(200) NOT NULL,
lang varchar(20) NOT NULL,
sending_type varchar(20) NOT NULL,
action varchar(200) NOT NULL,
schedules text NOT NULL,
resa_status varchar(200) NOT NULL,
resa_payment_status varchar(200) NOT NULL,
multiple_accom tinyint(1) NOT NULL,
all_accom tinyint(1) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->email_templates_accom_table} (
email_template_id bigint(20) NOT NULL,
accom_id bigint(20) NOT NULL,
PRIMARY KEY  (email_template_id, accom_id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->document_templates_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
name varchar(200) NOT NULL,
content longtext NOT NULL,
lang varchar(20) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->accom_blocked_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
accom_id bigint(20) NOT NULL,
accom_all_ids tinyint(1) NOT NULL,
accom_num bigint(20) NOT NULL,
accom_all_num tinyint(1) NOT NULL,
from_date DATE NOT NULL,
to_date DATE NOT NULL,
linked_resa_id bigint(20) NOT NULL,
is_prepa_time tinyint(1) NOT NULL,
comment text NOT NULL,
uid varchar(256) NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->ical_table} (
synchro_id varchar(128) NOT NULL,
synchro_url varchar(512) NOT NULL,
accom_id bigint(20) NOT NULL,
accom_num bigint(20) NOT NULL,
calendar_id varchar(256) NOT NULL,
calendar_name varchar(50) NOT NULL,
PRIMARY KEY  (synchro_id)
) $this->charset_collate;

CREATE TABLE {$this->hbdb->sync_errors_table} (
error_type varchar(50) NOT NULL,
synchro_url varchar(512) NOT NULL,
uid varchar(256) NOT NULL,
calendar_name varchar(50) NOT NULL,
accom_id bigint(20) NOT NULL,
accom_num bigint(20) NOT NULL,
check_in date NOT NULL,
check_out date NOT NULL,
created_on DATETIME NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->booking_rules_seasons_table} (
rule_id bigint(20) NOT NULL,
season_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->discounts_seasons_table} (
discount_id bigint(20) NOT NULL,
season_id bigint(20) NOT NULL
) $this->charset_collate;

CREATE TABLE {$this->hbdb->email_logs_table} (
id bigint(20) NOT NULL AUTO_INCREMENT,
trigger_by varchar(20) NOT NULL,
trigger_by_details varchar(200) NOT NULL,
resa_id bigint(20) NOT NULL,
resa_is_parent tinyint(1) NOT NULL,
template_id bigint(20) NOT NULL,
error_msg text NOT NULL,
wp_error text NOT NULL,
sent_on DATETIME NOT NULL,
PRIMARY KEY  (id)
) $this->charset_collate;
";

	}

}