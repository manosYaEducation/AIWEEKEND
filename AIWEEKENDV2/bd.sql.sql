use startup_selfie

CREATE TABLE IF NOT EXISTS imagenes (
    id INT NOT NULL AUTO_INCREMENT,
    url LONGBLOB,      -- Hasta 4GB de datos binarios
    url_carta LONGBLOB, -- Hasta 4GB de datos binarios
    mensaje VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;