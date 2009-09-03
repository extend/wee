CREATE TABLE roles
(
   role_id serial NOT NULL, 
   perm_operation character varying(8), 
   perm_resource character varying(8), 
   CONSTRAINT pk_aefe_roles PRIMARY KEY (role_id), 
) WITH (OIDS=FALSE);

CREATE TABLE aefe_user_role
(
   user_id integer NOT NULL, 
   role_id integer NOT NULL, 
   CONSTRAINT pk_aefe_user_role PRIMARY KEY (user_id, role_id), 
   CONSTRAINT fk_aefe_roles_role_id FOREIGN KEY (role_id) REFERENCES aefe_roles (role_id)    ON UPDATE NO ACTION ON DELETE CASCADE
) WITH (OIDS=FALSE);
