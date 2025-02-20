CREATE TABLE IF NOT EXISTS imagenes (
  id int NOT NULL AUTO_INCREMENT,
  url longblob,
  url_ia longblob,
  observacion varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--Para insercion

