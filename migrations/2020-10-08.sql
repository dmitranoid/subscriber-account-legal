alter table user
	add region string;

update user
set region='bares'
where
      user.region is null;
