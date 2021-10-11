CREATE DATABASE lumen
    ENCODING = 'utf8'
    LC_COLLATE = 'en_US.utf8'
    LC_CTYPE = 'en_US.utf8'
;

CREATE DATABASE test_lumen
    ENCODING = 'utf8'
    LC_COLLATE = 'en_US.utf8'
    LC_CTYPE = 'en_US.utf8'
;

CREATE USER lumen WITH PASSWORD 'lumen';

GRANT ALL PRIVILEGES ON DATABASE lumen TO lumen;
GRANT ALL PRIVILEGES ON DATABASE test_lumen TO lumen;
