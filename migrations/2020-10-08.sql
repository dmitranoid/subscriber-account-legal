alter table user
	add region string;

update user
    set region='bar'
where
      user.region is null;
