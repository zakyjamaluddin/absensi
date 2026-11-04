-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: absensi
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `absensis`
--

DROP TABLE IF EXISTS `absensis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `absensis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `absensable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `absensable_id` bigint unsigned NOT NULL,
  `jam_masuk` time DEFAULT NULL,
  `status_masuk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `status_pulang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensis_tanggal_absensable_id_absensable_type_unique` (`tanggal`,`absensable_id`,`absensable_type`),
  KEY `absensis_absensable_type_absensable_id_index` (`absensable_type`,`absensable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `absensis`
--

LOCK TABLES `absensis` WRITE;
/*!40000 ALTER TABLE `absensis` DISABLE KEYS */;
INSERT INTO `absensis` VALUES (1,'2026-07-20','App\\Models\\Siswa',1,NULL,'Tepat Waktu','12:13:00',NULL,'2026-07-20 05:13:47','2026-07-20 06:35:23'),(2,'2026-07-20','App\\Models\\Siswa',2,NULL,'Terlambat',NULL,NULL,'2026-07-20 05:15:14','2026-07-20 07:35:43'),(4,'2026-07-20','App\\Models\\Guru',1,NULL,NULL,'14:06:03',NULL,'2026-07-20 07:06:03','2026-07-20 07:06:03'),(5,'2026-07-20','App\\Models\\Guru',2,NULL,NULL,'14:07:04',NULL,'2026-07-20 07:07:04','2026-07-20 07:07:04'),(6,'2026-07-20','App\\Models\\Siswa',3,NULL,NULL,'14:11:31',NULL,'2026-07-20 07:11:31','2026-07-20 07:11:31'),(15,'2026-09-05','App\\Models\\Siswa',1,NULL,'Alpa',NULL,NULL,'2026-09-05 07:24:16','2026-09-05 07:24:16'),(16,'2026-09-05','App\\Models\\Siswa',2,NULL,'Alpa',NULL,NULL,'2026-09-05 07:24:16','2026-09-05 07:24:16'),(17,'2026-09-05','App\\Models\\Siswa',3,NULL,'Alpa',NULL,NULL,'2026-09-05 07:24:16','2026-09-05 07:24:16'),(18,'2026-09-05','App\\Models\\Siswa',4,NULL,'Alpa',NULL,NULL,'2026-09-05 07:24:16','2026-09-05 07:24:16'),(19,'2026-09-28','App\\Models\\Siswa',1,NULL,'Alpa',NULL,NULL,'2026-09-28 02:57:29','2026-09-28 02:57:29'),(20,'2026-09-28','App\\Models\\Siswa',2,NULL,'Alpa',NULL,NULL,'2026-09-28 02:57:29','2026-09-28 02:57:29'),(21,'2026-09-28','App\\Models\\Siswa',3,NULL,'Alpa',NULL,NULL,'2026-09-28 02:57:29','2026-09-28 02:57:29'),(22,'2026-09-28','App\\Models\\Siswa',4,NULL,'Alpa',NULL,NULL,'2026-09-28 02:57:29','2026-09-28 02:57:29'),(40,'2026-10-25','App\\Models\\Siswa',1,NULL,NULL,'20:38:50','Pulang','2026-10-25 13:38:50','2026-10-25 13:38:50'),(42,'2026-10-28','App\\Models\\Siswa',1,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:40','2026-10-28 16:50:40'),(43,'2026-10-28','App\\Models\\Siswa',2,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:40','2026-10-28 16:50:40'),(44,'2026-10-28','App\\Models\\Siswa',3,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:40','2026-10-28 16:50:40'),(45,'2026-10-28','App\\Models\\Siswa',4,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:40','2026-10-28 16:50:40'),(46,'2026-10-28','App\\Models\\Guru',1,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:40','2026-10-28 16:50:40'),(47,'2026-10-28','App\\Models\\Guru',2,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:40','2026-10-28 16:50:40'),(48,'2026-10-28','App\\Models\\Guru',3,NULL,'Belum Absen',NULL,NULL,'2026-10-28 16:50:41','2026-10-28 16:50:41'),(49,'2026-10-29','App\\Models\\Siswa',1,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(50,'2026-10-29','App\\Models\\Siswa',2,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(51,'2026-10-29','App\\Models\\Siswa',3,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(52,'2026-10-29','App\\Models\\Siswa',4,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(53,'2026-10-29','App\\Models\\Guru',1,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(54,'2026-10-29','App\\Models\\Guru',2,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(55,'2026-10-29','App\\Models\\Guru',3,NULL,'Belum Absen',NULL,NULL,'2026-10-28 17:14:19','2026-10-28 17:14:19'),(61,'2026-10-30','App\\Models\\Siswa',1,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(62,'2026-10-30','App\\Models\\Siswa',2,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(63,'2026-10-30','App\\Models\\Siswa',3,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(64,'2026-10-30','App\\Models\\Siswa',4,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(65,'2026-10-30','App\\Models\\Guru',1,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(66,'2026-10-30','App\\Models\\Guru',2,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(67,'2026-10-30','App\\Models\\Guru',3,NULL,'Belum Absen',NULL,NULL,'2026-10-30 08:54:53','2026-10-30 08:54:53'),(68,'2026-11-02','App\\Models\\Siswa',1,NULL,'Belum Absen',NULL,NULL,'2026-11-02 13:50:20','2026-11-02 13:50:20'),(69,'2026-11-02','App\\Models\\Siswa',2,NULL,'Belum Absen',NULL,NULL,'2026-11-02 13:50:20','2026-11-02 13:50:20'),(70,'2026-11-02','App\\Models\\Siswa',4,NULL,'Belum Absen',NULL,NULL,'2026-11-02 13:50:20','2026-11-02 13:50:20');
/*!40000 ALTER TABLE `absensis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bukus`
--

DROP TABLE IF EXISTS `bukus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bukus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_buku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penulis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penerbit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_terbit` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stok` int NOT NULL DEFAULT '1',
  `tersedia` int NOT NULL DEFAULT '1',
  `rak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bukus_kode_buku_unique` (`kode_buku`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bukus`
--

LOCK TABLES `bukus` WRITE;
/*!40000 ALTER TABLE `bukus` DISABLE KEYS */;
INSERT INTO `bukus` VALUES (3,'123','buku test','zky','zaky','2026','Pelajaran',10,9,NULL,NULL,'2026-10-25 11:38:11','2026-10-25 11:38:57'),(4,'BK-001','Fiqih Sunnah Praktis','Sayyid Sabiq','Darul Kutub','2022','Agama Islam',10,10,'Rak A-1',NULL,'2026-11-02 14:57:15','2026-11-02 14:57:15'),(5,'BK-002','Laskar Pelangi','Andrea Hirata','Bentang Pustaka','2018','Sastra / Novel',5,5,'Rak B-2',NULL,'2026-11-02 14:57:15','2026-11-02 14:57:15');
/*!40000 ALTER TABLE `bukus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exports`
--

DROP TABLE IF EXISTS `exports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exporter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exports_user_id_foreign` (`user_id`),
  CONSTRAINT `exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exports`
--

LOCK TABLES `exports` WRITE;
/*!40000 ALTER TABLE `exports` DISABLE KEYS */;
/*!40000 ALTER TABLE `exports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_import_rows`
--

DROP TABLE IF EXISTS `failed_import_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_import_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL,
  `import_id` bigint unsigned NOT NULL,
  `validation_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_import_rows_import_id_foreign` (`import_id`),
  CONSTRAINT `failed_import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_import_rows`
--

LOCK TABLES `failed_import_rows` WRITE;
/*!40000 ALTER TABLE `failed_import_rows` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_import_rows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gurus`
--

DROP TABLE IF EXISTS `gurus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gurus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_notif_wa_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gurus_nip_unique` (`nip`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gurus`
--

LOCK TABLES `gurus` WRITE;
/*!40000 ALTER TABLE `gurus` DISABLE KEYS */;
INSERT INTO `gurus` VALUES (1,'19810101','Ustadz Ahmad Fauzi',NULL,'081234567890',1,'2026-07-20 03:17:53','2026-07-20 03:17:53'),(2,'19850202','Ustadz Muhammad Ridwan',NULL,'081234567891',1,'2026-07-20 03:17:53','2026-07-20 03:17:53'),(3,'19900303','Budi Santoso, S.Pd',NULL,'081234567892',1,'2026-09-05 07:22:36','2026-09-05 07:22:36');
/*!40000 ALTER TABLE `gurus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hari_liburs`
--

DROP TABLE IF EXISTS `hari_liburs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hari_liburs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hari_liburs_tanggal_unique` (`tanggal`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hari_liburs`
--

LOCK TABLES `hari_liburs` WRITE;
/*!40000 ALTER TABLE `hari_liburs` DISABLE KEYS */;
INSERT INTO `hari_liburs` VALUES (1,'2026-07-21','Libur Asal Saja','2026-07-20 07:12:43','2026-07-20 07:12:43'),(2,'2026-01-01','Hari Tahun Baru','2026-10-30 08:51:36','2026-10-30 08:51:36'),(3,'2026-02-17','Tahun Baru Imlek','2026-10-30 08:51:37','2026-10-30 08:51:37'),(4,'2026-04-03','Wafat Isa Almasih','2026-10-30 08:51:39','2026-10-30 08:51:39'),(5,'2026-05-01','Hari Buruh Internasional / Pekerja','2026-10-30 08:51:39','2026-10-30 08:51:39'),(6,'2026-05-14','Kenaikan Isa Al Masih','2026-10-30 08:51:39','2026-10-30 08:51:39'),(7,'2026-06-01','Hari Lahir Pancasila','2026-10-30 08:51:39','2026-10-30 08:51:39'),(8,'2026-08-17','Hari Proklamasi Kemerdekaan R.I.','2026-10-30 08:51:39','2026-10-30 08:51:39'),(9,'2026-12-25','Hari Raya Natal','2026-10-30 08:51:39','2026-10-30 08:51:39'),(10,'2026-02-16','Cuti Bersama Tahun Baru Imlek','2026-10-30 08:51:39','2026-10-30 08:51:39'),(11,'2026-03-18','Cuti Bersama Hari Suci Nyepi (Tahun Baru Saka)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(12,'2026-03-19','Hari Suci Nyepi (Tahun Baru Saka)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(13,'2026-03-20','Cuti Bersama Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(14,'2026-03-23','Cuti Bersama Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(15,'2026-03-24','Cuti Bersama Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(16,'2026-04-05','Hari Paskah','2026-10-30 08:51:39','2026-10-30 08:51:39'),(17,'2026-05-15','Cuti Bersama Kenaikan Isa Al Masih','2026-10-30 08:51:39','2026-10-30 08:51:39'),(18,'2026-05-28','Idul Adha (Lebaran Haji)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(19,'2026-12-24','Cuti Bersama Natal (Malam Natal)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(20,'2026-01-16','Isra Mikraj Nabi Muhammad','2026-10-30 08:51:39','2026-10-30 08:51:39'),(21,'2026-03-21','Hari Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(22,'2026-03-22','Hari Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(23,'2026-05-27','Idul Adha (Lebaran Haji)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(24,'2026-06-16','Hari Kedua Muharram','2026-10-30 08:51:39','2026-10-30 08:51:39'),(25,'2026-08-25','Maulid Nabi Muhammad','2026-10-30 08:51:39','2026-10-30 08:51:39'),(26,'2027-01-01','Hari Tahun Baru','2026-10-30 08:51:39','2026-10-30 08:51:39'),(27,'2027-02-06','Tahun Baru Imlek','2026-10-30 08:51:39','2026-10-30 08:51:39'),(28,'2027-03-26','Wafat Isa Almasih','2026-10-30 08:51:39','2026-10-30 08:51:39'),(29,'2027-05-01','Hari Buruh Internasional / Pekerja','2026-10-30 08:51:39','2026-10-30 08:51:39'),(30,'2027-05-06','Kenaikan Isa Al Masih','2026-10-30 08:51:39','2026-10-30 08:51:39'),(31,'2027-06-01','Hari Lahir Pancasila','2026-10-30 08:51:39','2026-10-30 08:51:39'),(32,'2027-08-15','Maulid Nabi Muhammad (belum pasti)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(33,'2027-08-17','Hari Proklamasi Kemerdekaan R.I.','2026-10-30 08:51:39','2026-10-30 08:51:39'),(34,'2027-12-25','Hari Raya Natal','2026-10-30 08:51:39','2026-10-30 08:51:39'),(35,'2026-05-31','Hari Raya Waisak','2026-10-30 08:51:39','2026-10-30 08:51:39'),(36,'2027-01-05','Isra Mikraj Nabi Muhammad (belum pasti)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(37,'2027-02-05','Cuti Bersama Tahun Baru Imlek','2026-10-30 08:51:39','2026-10-30 08:51:39'),(38,'2027-03-08','Hari Suci Nyepi (Tahun Baru Saka)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(39,'2027-03-09','Cuti Bersama Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(40,'2027-03-10','Hari Idul Fitri (belum pasti)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(41,'2027-03-11','Hari Idul Fitri (belum pasti)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(42,'2027-03-12','Cuti Bersama Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(43,'2027-03-15','Cuti Bersama Idul Fitri','2026-10-30 08:51:39','2026-10-30 08:51:39'),(44,'2027-03-25','Joint Holiday for Good Friday','2026-10-30 08:51:39','2026-10-30 08:51:39'),(45,'2027-05-17','Idul Adha (Lebaran Haji) (belum pasti)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(46,'2027-05-18','Idul Adha (Lebaran Haji)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(47,'2027-05-19','Cuti Bersama Waisak','2026-10-30 08:51:39','2026-10-30 08:51:39'),(48,'2027-05-20','Hari Raya Waisak','2026-10-30 08:51:39','2026-10-30 08:51:39'),(49,'2027-06-06','Satu Muharam / Tahun Baru Hijriah (belum pasti)','2026-10-30 08:51:39','2026-10-30 08:51:39'),(50,'2027-12-24','Cuti Bersama Natal (Malam Natal)','2026-10-30 08:51:39','2026-10-30 08:51:39');
/*!40000 ALTER TABLE `hari_liburs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imports`
--

DROP TABLE IF EXISTS `imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imports_user_id_foreign` (`user_id`),
  CONSTRAINT `imports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imports`
--

LOCK TABLES `imports` WRITE;
/*!40000 ALTER TABLE `imports` DISABLE KEYS */;
/*!40000 ALTER TABLE `imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_guru_pikets`
--

DROP TABLE IF EXISTS `jadwal_guru_pikets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_guru_pikets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Ahad') COLLATE utf8mb4_unicode_ci NOT NULL,
  `guru_id` bigint unsigned NOT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_guru_pikets_guru_id_foreign` (`guru_id`),
  KEY `jadwal_guru_pikets_hari_guru_id_index` (`hari`,`guru_id`),
  CONSTRAINT `jadwal_guru_pikets_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_guru_pikets`
--

LOCK TABLES `jadwal_guru_pikets` WRITE;
/*!40000 ALTER TABLE `jadwal_guru_pikets` DISABLE KEYS */;
INSERT INTO `jadwal_guru_pikets` VALUES (1,'Kamis',3,NULL,1,'2026-10-29 08:59:30','2026-10-29 08:59:30'),(2,'Jumat',3,NULL,1,'2026-10-30 07:16:05','2026-10-30 07:16:05'),(4,'Senin',1,'Pos Gerbang Utama',1,'2026-11-02 14:57:15','2026-11-02 14:57:15'),(5,'Selasa',2,'Pos Gerbang Barat',1,'2026-11-02 14:57:15','2026-11-02 14:57:15');
/*!40000 ALTER TABLE `jadwal_guru_pikets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jadwal_mengajars`
--

DROP TABLE IF EXISTS `jadwal_mengajars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jadwal_mengajars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guru_id` bigint unsigned NOT NULL,
  `hari` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` bigint unsigned DEFAULT NULL,
  `mata_pelajaran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_ke` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mulai_jam_ke` int unsigned DEFAULT NULL,
  `sampai_jam_ke` int unsigned DEFAULT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_mengajars_kelas_id_foreign` (`kelas_id`),
  KEY `jadwal_mengajars_guru_id_hari_index` (`guru_id`,`hari`),
  CONSTRAINT `jadwal_mengajars_guru_id_foreign` FOREIGN KEY (`guru_id`) REFERENCES `gurus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwal_mengajars_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jadwal_mengajars`
--

LOCK TABLES `jadwal_mengajars` WRITE;
/*!40000 ALTER TABLE `jadwal_mengajars` DISABLE KEYS */;
/*!40000 ALTER TABLE `jadwal_mengajars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jam_pelajarans`
--

DROP TABLE IF EXISTS `jam_pelajarans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jam_pelajarans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `jam_ke` int unsigned NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jam_pelajarans`
--

LOCK TABLES `jam_pelajarans` WRITE;
/*!40000 ALTER TABLE `jam_pelajarans` DISABLE KEYS */;
INSERT INTO `jam_pelajarans` VALUES (1,1,'07:00:00','07:45:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(2,2,'07:45:00','08:30:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(3,3,'08:30:00','09:15:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(4,4,'09:15:00','10:00:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(5,5,'10:15:00','11:00:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(6,6,'11:00:00','11:45:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(7,7,'12:30:00','13:15:00','2026-10-24 15:59:06','2026-10-24 15:59:06'),(8,8,'13:15:00','14:00:00','2026-10-24 15:59:06','2026-10-24 15:59:06');
/*!40000 ALTER TABLE `jam_pelajarans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jam_settings`
--

DROP TABLE IF EXISTS `jam_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jam_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `mulai_masuk` time NOT NULL,
  `selesai_masuk` time NOT NULL,
  `batas_terlambat` time NOT NULL DEFAULT '07:00:00',
  `mode_absensi_guru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'statis',
  `aturan_terlambat_guru` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'jadwal_mengajar',
  `batas_terlambat_guru` time DEFAULT NULL,
  `mulai_pulang` time NOT NULL,
  `selesai_pulang` time NOT NULL,
  `libur_pekanan` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jam_settings`
--

LOCK TABLES `jam_settings` WRITE;
/*!40000 ALTER TABLE `jam_settings` DISABLE KEYS */;
INSERT INTO `jam_settings` VALUES (1,'06:00:00','11:59:00','07:00:00','statis','jadwal_mengajar',NULL,'12:00:00','21:00:00','[\"Sunday\"]','2026-07-20 03:17:53','2026-09-29 07:37:53');
/*!40000 ALTER TABLE `jam_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"4fb4fd68-893d-4d2b-b106-548f1c34ee1c\",\"displayName\":\"Filament\\\\Actions\\\\Imports\\\\Jobs\\\\ImportCsv\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":5,\"failOnTimeout\":false,\"backoff\":\"60,120,300,600\",\"timeout\":null,\"retryUntil\":1784691718,\"deleteWhenMissingModels\":true,\"data\":{\"commandName\":\"Filament\\\\Actions\\\\Imports\\\\Jobs\\\\ImportCsv\",\"command\":\"O:39:\\\"Filament\\\\Actions\\\\Imports\\\\Jobs\\\\ImportCsv\\\":6:{s:11:\\\"\\u0000*\\u0000importer\\\";O:34:\\\"App\\\\Filament\\\\Imports\\\\SiswaImporter\\\":3:{s:9:\\\"\\u0000*\\u0000import\\\";O:38:\\\"Filament\\\\Actions\\\\Imports\\\\Models\\\\Import\\\":35:{s:13:\\\"\\u0000*\\u0000connection\\\";s:5:\\\"mysql\\\";s:8:\\\"\\u0000*\\u0000table\\\";N;s:13:\\\"\\u0000*\\u0000primaryKey\\\";s:2:\\\"id\\\";s:10:\\\"\\u0000*\\u0000keyType\\\";s:3:\\\"int\\\";s:12:\\\"incrementing\\\";b:1;s:7:\\\"\\u0000*\\u0000with\\\";a:0:{}s:12:\\\"\\u0000*\\u0000withCount\\\";a:0:{}s:19:\\\"preventsLazyLoading\\\";b:0;s:10:\\\"\\u0000*\\u0000perPage\\\";i:15;s:6:\\\"exists\\\";b:1;s:18:\\\"wasRecentlyCreated\\\";b:1;s:28:\\\"\\u0000*\\u0000escapeWhenCastingToString\\\";b:0;s:13:\\\"\\u0000*\\u0000attributes\\\";a:8:{s:7:\\\"user_id\\\";i:1;s:9:\\\"file_name\\\";s:15:\\\"test import.csv\\\";s:9:\\\"file_path\\\";s:100:\\\"C:\\\\laragon\\\\www\\\\absensi\\\\storage\\\\app\\/private\\\\livewire-tmp\\/a8QpuTSTqKODhH8M6O7qPtsiWJ1kJZeFkshNowG9.csv\\\";s:8:\\\"importer\\\";s:34:\\\"App\\\\Filament\\\\Imports\\\\SiswaImporter\\\";s:10:\\\"total_rows\\\";i:1;s:10:\\\"updated_at\\\";s:19:\\\"2026-07-21 10:41:58\\\";s:10:\\\"created_at\\\";s:19:\\\"2026-07-21 10:41:58\\\";s:2:\\\"id\\\";i:1;}s:11:\\\"\\u0000*\\u0000original\\\";a:8:{s:7:\\\"user_id\\\";i:1;s:9:\\\"file_name\\\";s:15:\\\"test import.csv\\\";s:9:\\\"file_path\\\";s:100:\\\"C:\\\\laragon\\\\www\\\\absensi\\\\storage\\\\app\\/private\\\\livewire-tmp\\/a8QpuTSTqKODhH8M6O7qPtsiWJ1kJZeFkshNowG9.csv\\\";s:8:\\\"importer\\\";s:34:\\\"App\\\\Filament\\\\Imports\\\\SiswaImporter\\\";s:10:\\\"total_rows\\\";i:1;s:10:\\\"updated_at\\\";s:19:\\\"2026-07-21 10:41:58\\\";s:10:\\\"created_at\\\";s:19:\\\"2026-07-21 10:41:58\\\";s:2:\\\"id\\\";i:1;}s:10:\\\"\\u0000*\\u0000changes\\\";a:0:{}s:11:\\\"\\u0000*\\u0000previous\\\";a:0:{}s:8:\\\"\\u0000*\\u0000casts\\\";a:4:{s:12:\\\"completed_at\\\";s:9:\\\"timestamp\\\";s:14:\\\"processed_rows\\\";s:7:\\\"integer\\\";s:10:\\\"total_rows\\\";s:7:\\\"integer\\\";s:15:\\\"successful_rows\\\";s:7:\\\"integer\\\";}s:17:\\\"\\u0000*\\u0000classCastCache\\\";a:0:{}s:21:\\\"\\u0000*\\u0000attributeCastCache\\\";a:0:{}s:13:\\\"\\u0000*\\u0000dateFormat\\\";N;s:10:\\\"\\u0000*\\u0000appends\\\";a:0:{}s:19:\\\"\\u0000*\\u0000dispatchesEvents\\\";a:0:{}s:14:\\\"\\u0000*\\u0000observables\\\";a:0:{}s:12:\\\"\\u0000*\\u0000relations\\\";a:0:{}s:10:\\\"\\u0000*\\u0000touches\\\";a:0:{}s:27:\\\"\\u0000*\\u0000relationAutoloadCallback\\\";N;s:26:\\\"\\u0000*\\u0000relationAutoloadContext\\\";N;s:10:\\\"timestamps\\\";b:1;s:13:\\\"usesUniqueIds\\\";b:0;s:9:\\\"\\u0000*\\u0000hidden\\\";a:0:{}s:10:\\\"\\u0000*\\u0000visible\\\";a:0:{}s:11:\\\"\\u0000*\\u0000fillable\\\";a:0:{}s:10:\\\"\\u0000*\\u0000guarded\\\";a:0:{}s:12:\\\"\\u0000*\\u0000columnMap\\\";a:3:{s:3:\\\"nis\\\";s:3:\\\"nis\\\";s:4:\\\"nama\\\";s:4:\\\"nama\\\";s:8:\\\"kelas_id\\\";s:8:\\\"kelas_id\\\";}s:10:\\\"\\u0000*\\u0000options\\\";a:0:{}}s:12:\\\"\\u0000*\\u0000columnMap\\\";a:3:{s:3:\\\"nis\\\";s:3:\\\"nis\\\";s:4:\\\"nama\\\";s:4:\\\"nama\\\";s:8:\\\"kelas_id\\\";s:8:\\\"kelas_id\\\";}s:10:\\\"\\u0000*\\u0000options\\\";a:0:{}}s:9:\\\"\\u0000*\\u0000import\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:38:\\\"Filament\\\\Actions\\\\Imports\\\\Models\\\\Import\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:7:\\\"\\u0000*\\u0000rows\\\";s:108:\\\"YToxOntpOjA7YTozOntzOjM6Im5pcyI7czozOiIxMjMiO3M6NDoibmFtYSI7czo0OiJ6YWt5IjtzOjg6ImtlbGFzX2lkIjtzOjE6IjEiO319\\\";s:12:\\\"\\u0000*\\u0000columnMap\\\";a:3:{s:3:\\\"nis\\\";s:3:\\\"nis\\\";s:4:\\\"nama\\\";s:4:\\\"nama\\\";s:8:\\\"kelas_id\\\";s:8:\\\"kelas_id\\\";}s:10:\\\"\\u0000*\\u0000options\\\";a:0:{}s:7:\\\"batchId\\\";s:36:\\\"a24f149b-546c-4803-b542-0eeaab4c5cd3\\\";}\",\"batchId\":\"a24f149b-546c-4803-b542-0eeaab4c5cd3\"},\"createdAt\":1784605318,\"delay\":null}',0,NULL,1784605318,1784605318);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wali_kelas_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`),
  CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `gurus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (1,'Kelas 10-A (Ula)',1,'2026-07-20 03:17:53','2026-07-20 03:17:53'),(2,'Kelas 10-B (Ula)',2,'2026-07-20 03:17:53','2026-07-20 03:17:53');
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kunjungan_perpustakaans`
--

DROP TABLE IF EXISTS `kunjungan_perpustakaans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kunjungan_perpustakaans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_masuk` time NOT NULL,
  `keperluan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Membaca / Belajar',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kunjungan_perpustakaans_siswa_id_foreign` (`siswa_id`),
  KEY `kunjungan_perpustakaans_tanggal_index` (`tanggal`),
  CONSTRAINT `kunjungan_perpustakaans_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kunjungan_perpustakaans`
--

LOCK TABLES `kunjungan_perpustakaans` WRITE;
/*!40000 ALTER TABLE `kunjungan_perpustakaans` DISABLE KEYS */;
INSERT INTO `kunjungan_perpustakaans` VALUES (5,1,'2026-10-25','18:39:55','Membaca / Belajar','2026-10-25 11:39:55','2026-10-25 11:39:55'),(6,4,'2026-10-25','21:18:25','Membaca / Belajar','2026-10-25 14:18:25','2026-10-25 14:18:25');
/*!40000 ALTER TABLE `kunjungan_perpustakaans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_07_17_062143_create_gurus_table',1),(5,'2026_07_17_062152_create_kelas_table',1),(6,'2026_07_17_062200_create_siswas_table',1),(7,'2026_07_17_062209_create_jam_settings_table',1),(8,'2026_07_17_062218_create_absensis_table',1),(9,'2026_07_17_185053_add_batas_terlambat_and_statuses_to_tables',1),(10,'2026_07_18_152809_create_hari_liburs_table',1),(11,'2026_07_20_094551_add_userable_to_users_table',1),(13,'2026_07_20_103235_create_permission_tables',2),(14,'2026_07_21_103944_create_imports_table',3),(15,'2026_07_21_103945_create_exports_table',3),(16,'2026_07_21_103946_create_failed_import_rows_table',3),(17,'2026_09_04_152209_create_wa_settings_table',4),(18,'2026_09_25_154810_add_alpa_settings_to_wa_settings_table',5),(19,'2026_09_29_141814_add_libur_pekanan_to_jam_settings_table',6),(20,'2026_09_29_142211_change_libur_pekanan_to_json_in_jam_settings_table',7),(21,'2026_10_23_160000_add_wali_fields_to_siswas_table',8),(22,'2026_10_23_160001_add_notif_wali_toggles_to_wa_settings_table',8),(23,'2026_10_23_170000_create_perpustakaan_tables',9),(24,'2026_10_24_230000_create_jadwal_mengajars_table',10),(25,'2026_10_24_230001_add_guru_rules_to_jam_settings_wa_settings_and_gurus_table',10),(26,'2026_10_24_235000_create_jam_pelajarans_and_update_jadwal_mengajars',11),(27,'2026_10_25_194330_add_foto_to_siswas_table',12),(28,'2026_10_29_002500_add_foto_to_gurus_table',13),(29,'2026_10_29_154134_create_notifications_table',14),(30,'2026_10_29_160000_add_google_id_to_users_table',14),(31,'2026_10_29_160100_create_jadwal_guru_pikets_table',14),(32,'2026_10_29_160200_create_pengajuan_izins_table',14);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',11),(1,'App\\Models\\User',12),(3,'App\\Models\\User',13),(4,'App\\Models\\User',14),(5,'App\\Models\\User',15),(6,'App\\Models\\User',16),(7,'App\\Models\\User',17),(8,'App\\Models\\User',18),(9,'App\\Models\\User',19);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_bukus`
--

DROP TABLE IF EXISTS `peminjaman_bukus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peminjaman_bukus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_peminjaman` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `siswa_id` bigint unsigned NOT NULL,
  `buku_id` bigint unsigned NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('Dipinjam','Dikembalikan','Terlambat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dipinjam',
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `peminjaman_bukus_kode_peminjaman_unique` (`kode_peminjaman`),
  KEY `peminjaman_bukus_siswa_id_foreign` (`siswa_id`),
  KEY `peminjaman_bukus_buku_id_foreign` (`buku_id`),
  KEY `peminjaman_bukus_tanggal_pinjam_index` (`tanggal_pinjam`),
  KEY `peminjaman_bukus_tanggal_jatuh_tempo_index` (`tanggal_jatuh_tempo`),
  KEY `peminjaman_bukus_tanggal_kembali_index` (`tanggal_kembali`),
  CONSTRAINT `peminjaman_bukus_buku_id_foreign` FOREIGN KEY (`buku_id`) REFERENCES `bukus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_bukus_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_bukus`
--

LOCK TABLES `peminjaman_bukus` WRITE;
/*!40000 ALTER TABLE `peminjaman_bukus` DISABLE KEYS */;
INSERT INTO `peminjaman_bukus` VALUES (3,'PJ-261025-QTQU',4,3,'2026-10-25','2026-11-01',NULL,'Dipinjam',NULL,'2026-10-25 11:38:57','2026-10-25 11:38:57');
/*!40000 ALTER TABLE `peminjaman_bukus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengajuan_izins`
--

DROP TABLE IF EXISTS `pengajuan_izins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengajuan_izins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `izinable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `izinable_id` bigint unsigned NOT NULL,
  `tipe_izin` enum('Izin','Sakit') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Izin',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `alasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bukti_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Menunggu',
  `disetujui_oleh_id` bigint unsigned DEFAULT NULL,
  `catatan_petugas` text COLLATE utf8mb4_unicode_ci,
  `tanggal_approval` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_izins_izinable_type_izinable_id_index` (`izinable_type`,`izinable_id`),
  KEY `pengajuan_izins_disetujui_oleh_id_foreign` (`disetujui_oleh_id`),
  KEY `pengajuan_izins_status_tanggal_mulai_index` (`status`,`tanggal_mulai`),
  CONSTRAINT `pengajuan_izins_disetujui_oleh_id_foreign` FOREIGN KEY (`disetujui_oleh_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengajuan_izins`
--

LOCK TABLES `pengajuan_izins` WRITE;
/*!40000 ALTER TABLE `pengajuan_izins` DISABLE KEYS */;
/*!40000 ALTER TABLE `pengajuan_izins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'ViewAny:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(2,'View:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(3,'Create:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(4,'Update:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(5,'Delete:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(6,'DeleteAny:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(7,'Restore:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(8,'ForceDelete:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(9,'ForceDeleteAny:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(10,'RestoreAny:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(11,'Replicate:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(12,'Reorder:Absensi','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(13,'ViewAny:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(14,'View:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(15,'Create:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(16,'Update:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(17,'Delete:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(18,'DeleteAny:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(19,'Restore:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(20,'ForceDelete:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(21,'ForceDeleteAny:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(22,'RestoreAny:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(23,'Replicate:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(24,'Reorder:Guru','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(25,'ViewAny:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(26,'View:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(27,'Create:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(28,'Update:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(29,'Delete:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(30,'DeleteAny:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(31,'Restore:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(32,'ForceDelete:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(33,'ForceDeleteAny:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(34,'RestoreAny:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(35,'Replicate:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(36,'Reorder:HariLibur','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(37,'ViewAny:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(38,'View:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(39,'Create:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(40,'Update:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(41,'Delete:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(42,'DeleteAny:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(43,'Restore:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(44,'ForceDelete:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(45,'ForceDeleteAny:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(46,'RestoreAny:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(47,'Replicate:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(48,'Reorder:JamSetting','web','2026-07-20 03:47:19','2026-07-20 03:47:19'),(49,'ViewAny:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(50,'View:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(51,'Create:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(52,'Update:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(53,'Delete:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(54,'DeleteAny:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(55,'Restore:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(56,'ForceDelete:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(57,'ForceDeleteAny:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(58,'RestoreAny:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(59,'Replicate:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(60,'Reorder:Kelas','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(61,'ViewAny:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(62,'View:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(63,'Create:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(64,'Update:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(65,'Delete:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(66,'DeleteAny:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(67,'Restore:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(68,'ForceDelete:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(69,'ForceDeleteAny:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(70,'RestoreAny:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(71,'Replicate:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(72,'Reorder:Role','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(73,'ViewAny:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(74,'View:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(75,'Create:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(76,'Update:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(77,'Delete:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(78,'DeleteAny:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(79,'Restore:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(80,'ForceDelete:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(81,'ForceDeleteAny:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(82,'RestoreAny:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(83,'Replicate:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(84,'Reorder:Siswa','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(85,'ViewAny:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(86,'View:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(87,'Create:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(88,'Update:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(89,'Delete:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(90,'DeleteAny:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(91,'Restore:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(92,'ForceDelete:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(93,'ForceDeleteAny:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(94,'RestoreAny:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(95,'Replicate:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(96,'Reorder:User','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(97,'View:ScanAbsensi','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(98,'View:AttendanceStatsOverview','web','2026-07-20 03:47:20','2026-07-20 03:47:20'),(99,'ViewAny:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(100,'View:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(101,'Create:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(102,'Update:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(103,'Delete:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(104,'DeleteAny:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(105,'Restore:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(106,'ForceDelete:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(107,'ForceDeleteAny:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(108,'RestoreAny:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(109,'Replicate:WaSetting','web','2026-09-04 08:42:28','2026-09-04 08:42:28'),(110,'Reorder:WaSetting','web','2026-09-04 08:42:29','2026-09-04 08:42:29'),(111,'View:HeaderGreetingWidget','web','2026-09-25 11:35:48','2026-09-25 11:35:48'),(112,'View:RecentAbsensisTable','web','2026-09-25 11:35:48','2026-09-25 11:35:48'),(113,'View:Dokumentasi','web','2026-09-25 11:51:32','2026-09-25 11:51:32'),(114,'View:PengaturanJam','web','2026-09-29 07:58:04','2026-09-29 07:58:04'),(115,'ViewAny:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(116,'View:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(117,'Create:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(118,'Update:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(119,'Delete:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(120,'DeleteAny:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(121,'Restore:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(122,'ForceDelete:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(123,'ForceDeleteAny:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(124,'RestoreAny:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(125,'Replicate:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(126,'Reorder:Buku','web','2026-10-28 23:13:19','2026-10-28 23:13:19'),(127,'ViewAny:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(128,'View:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(129,'Create:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(130,'Update:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(131,'Delete:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(132,'DeleteAny:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(133,'Restore:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(134,'ForceDelete:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(135,'ForceDeleteAny:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(136,'RestoreAny:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(137,'Replicate:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(138,'Reorder:JadwalMengajar','web','2026-10-28 23:13:21','2026-10-28 23:13:21'),(139,'ViewAny:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(140,'View:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(141,'Create:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(142,'Update:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(143,'Delete:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(144,'DeleteAny:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(145,'Restore:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(146,'ForceDelete:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(147,'ForceDeleteAny:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(148,'RestoreAny:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(149,'Replicate:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(150,'Reorder:KunjunganPerpustakaan','web','2026-10-28 23:13:22','2026-10-28 23:13:22'),(151,'ViewAny:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(152,'View:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(153,'Create:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(154,'Update:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(155,'Delete:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(156,'DeleteAny:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(157,'Restore:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(158,'ForceDelete:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(159,'ForceDeleteAny:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(160,'RestoreAny:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(161,'Replicate:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(162,'Reorder:PeminjamanBuku','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(163,'ViewAny:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(164,'View:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(165,'Create:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(166,'Update:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(167,'Delete:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(168,'DeleteAny:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(169,'Restore:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(170,'ForceDelete:PerpustakaanSetting','web','2026-10-28 23:13:23','2026-10-28 23:13:23'),(171,'ForceDeleteAny:PerpustakaanSetting','web','2026-10-28 23:13:24','2026-10-28 23:13:24'),(172,'RestoreAny:PerpustakaanSetting','web','2026-10-28 23:13:24','2026-10-28 23:13:24'),(173,'Replicate:PerpustakaanSetting','web','2026-10-28 23:13:24','2026-10-28 23:13:24'),(174,'Reorder:PerpustakaanSetting','web','2026-10-28 23:13:24','2026-10-28 23:13:24'),(175,'View:DashboardPerpustakaan','web','2026-10-28 23:13:28','2026-10-28 23:13:28'),(176,'View:LeaderboardKedisiplinan','web','2026-10-28 23:13:29','2026-10-28 23:13:29'),(177,'View:LeaderboardLiterasi','web','2026-10-28 23:13:29','2026-10-28 23:13:29'),(178,'View:ScanPerpustakaan','web','2026-10-28 23:13:31','2026-10-28 23:13:31'),(179,'View:Dashboard','web','2026-10-28 23:24:18','2026-10-28 23:24:18'),(180,'ViewAny:JadwalGuruPiket','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(181,'View:JadwalGuruPiket','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(182,'Create:JadwalGuruPiket','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(183,'Update:JadwalGuruPiket','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(184,'Delete:JadwalGuruPiket','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(185,'DeleteAny:JadwalGuruPiket','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(186,'ViewAny:PengajuanIzin','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(187,'View:PengajuanIzin','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(188,'Create:PengajuanIzin','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(189,'Update:PengajuanIzin','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(190,'Delete:PengajuanIzin','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(191,'DeleteAny:PengajuanIzin','web','2026-10-29 08:57:43','2026-10-29 08:57:43');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perpustakaan_settings`
--

DROP TABLE IF EXISTS `perpustakaan_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `perpustakaan_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lama_pinjam_default` int NOT NULL DEFAULT '7',
  `poin_kunjungan` int NOT NULL DEFAULT '5',
  `poin_peminjaman` int NOT NULL DEFAULT '15',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perpustakaan_settings`
--

LOCK TABLES `perpustakaan_settings` WRITE;
/*!40000 ALTER TABLE `perpustakaan_settings` DISABLE KEYS */;
INSERT INTO `perpustakaan_settings` VALUES (1,7,5,15,'2026-10-23 09:05:45','2026-10-23 09:05:45');
/*!40000 ALTER TABLE `perpustakaan_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1),(60,1),(61,1),(62,1),(63,1),(64,1),(65,1),(66,1),(67,1),(68,1),(69,1),(70,1),(71,1),(72,1),(73,1),(74,1),(75,1),(76,1),(77,1),(78,1),(79,1),(80,1),(81,1),(82,1),(83,1),(84,1),(85,1),(86,1),(87,1),(88,1),(89,1),(90,1),(91,1),(92,1),(93,1),(94,1),(95,1),(96,1),(97,1),(98,1),(99,1),(100,1),(101,1),(102,1),(103,1),(104,1),(105,1),(106,1),(107,1),(108,1),(109,1),(110,1),(111,1),(112,1),(113,1),(114,1),(115,1),(116,1),(117,1),(118,1),(119,1),(120,1),(121,1),(122,1),(123,1),(124,1),(125,1),(126,1),(127,1),(128,1),(129,1),(130,1),(131,1),(132,1),(133,1),(134,1),(135,1),(136,1),(137,1),(138,1),(139,1),(140,1),(141,1),(142,1),(143,1),(144,1),(145,1),(146,1),(147,1),(148,1),(149,1),(150,1),(151,1),(152,1),(153,1),(154,1),(155,1),(156,1),(157,1),(158,1),(159,1),(160,1),(161,1),(162,1),(163,1),(164,1),(165,1),(166,1),(167,1),(168,1),(169,1),(170,1),(171,1),(172,1),(173,1),(174,1),(175,1),(176,1),(177,1),(178,1),(179,1),(180,1),(181,1),(182,1),(183,1),(184,1),(185,1),(186,1),(187,1),(188,1),(189,1),(190,1),(191,1),(1,2),(2,2),(49,2),(50,2),(98,2),(111,2),(179,2),(186,2),(187,2),(188,2),(113,3),(115,3),(116,3),(117,3),(118,3),(119,3),(120,3),(121,3),(122,3),(123,3),(124,3),(125,3),(126,3),(139,3),(140,3),(141,3),(142,3),(143,3),(144,3),(145,3),(146,3),(147,3),(148,3),(149,3),(150,3),(151,3),(152,3),(153,3),(154,3),(155,3),(156,3),(157,3),(158,3),(159,3),(160,3),(161,3),(162,3),(163,3),(164,3),(165,3),(166,3),(167,3),(168,3),(169,3),(170,3),(171,3),(172,3),(173,3),(174,3),(175,3),(177,3),(178,3),(175,4),(178,4),(1,5),(2,5),(3,5),(4,5),(5,5),(6,5),(7,5),(8,5),(9,5),(10,5),(11,5),(12,5),(13,5),(14,5),(15,5),(16,5),(17,5),(18,5),(19,5),(20,5),(21,5),(22,5),(23,5),(24,5),(25,5),(26,5),(27,5),(28,5),(29,5),(30,5),(31,5),(32,5),(33,5),(34,5),(35,5),(36,5),(37,5),(38,5),(39,5),(40,5),(41,5),(42,5),(43,5),(44,5),(45,5),(46,5),(47,5),(48,5),(49,5),(50,5),(51,5),(52,5),(53,5),(54,5),(55,5),(56,5),(57,5),(58,5),(59,5),(60,5),(73,5),(74,5),(75,5),(76,5),(77,5),(78,5),(79,5),(80,5),(81,5),(82,5),(83,5),(84,5),(97,5),(98,5),(99,5),(100,5),(101,5),(102,5),(103,5),(104,5),(105,5),(106,5),(107,5),(108,5),(109,5),(110,5),(111,5),(112,5),(113,5),(127,5),(128,5),(129,5),(130,5),(131,5),(132,5),(133,5),(134,5),(135,5),(136,5),(137,5),(138,5),(176,5),(179,5),(180,5),(181,5),(182,5),(183,5),(184,5),(185,5),(186,5),(187,5),(188,5),(189,5),(190,5),(191,5),(97,6),(98,6),(111,6),(112,6),(179,6),(1,7),(2,7),(98,7),(111,7),(179,7),(186,7),(187,7),(188,7),(1,8),(2,8),(49,8),(50,8),(98,8),(111,8),(179,8),(186,8),(187,8),(188,8),(1,9),(2,9),(111,9),(179,9),(186,9),(187,9),(188,9);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'super_admin','web','2026-07-20 03:39:36','2026-07-20 03:39:36'),(2,'Wali Kelas','web','2026-07-20 07:50:20','2026-07-20 07:50:20'),(3,'admin_perpustakaan','web','2026-10-28 23:24:18','2026-10-28 23:24:18'),(4,'staff_perpustakaan','web','2026-10-28 23:24:18','2026-10-28 23:24:18'),(5,'admin_absensi','web','2026-10-28 23:24:18','2026-10-28 23:24:18'),(6,'staff_absensi','web','2026-10-28 23:24:18','2026-10-28 23:24:18'),(7,'guru','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(8,'wali_kelas','web','2026-10-29 08:57:43','2026-10-29 08:57:43'),(9,'siswa','web','2026-10-29 08:57:43','2026-10-29 08:57:43');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswas`
--

DROP TABLE IF EXISTS `siswas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_wa_wali` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_notif_wa_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `kelas_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswas_nis_unique` (`nis`),
  KEY `siswas_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `siswas_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswas`
--

LOCK TABLES `siswas` WRITE;
/*!40000 ALTER TABLE `siswas` DISABLE KEYS */;
INSERT INTO `siswas` VALUES (1,'20260001','Muhammad Al-Fatih',NULL,NULL,1,1,'2026-07-20 03:17:53','2026-07-20 03:17:53'),(2,'20260002','Ali bin Abi Thalib',NULL,NULL,1,1,'2026-07-20 03:17:53','2026-07-20 03:17:53'),(3,'20260003','Umar bin Khattab','siswas/01M5SNR3J85SEHRRRZ8EYH9KZ3.jpg',NULL,1,2,'2026-07-20 03:17:53','2026-10-25 13:59:35'),(4,'20260004','Ahmad Dani','siswas/01M5SPEHCWS63BN294D3BEGCDH.jpg',NULL,1,1,'2026-07-21 03:43:14','2026-10-25 14:11:50');
/*!40000 ALTER TABLE `siswas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `userable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `userable_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`),
  KEY `users_userable_type_userable_id_index` (`userable_type`,`userable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'Super Administrator','admin@maisba.sch.id',NULL,NULL,NULL,'$2y$12$Vhoa7Hk49wIUEW8nkYrtZuKTlydHfuwgTVXwZx3K7ryZ/0DM7glB2',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:20',NULL,NULL),(12,'Super Administrator (Alternatif)','superadmin@maisba.sch.id',NULL,NULL,NULL,'$2y$12$gTDqcziY3FnJ/W5VW9qZGeO/PCbxecwL/JXvaBNCYIf6p3oUyrk36',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:20',NULL,NULL),(13,'Admin Perpustakaan','admin.perpus@maisba.sch.id',NULL,NULL,NULL,'$2y$12$UqLveuxqMVE7SzYlEMWpGef5VSf2WTlkdchZzmO67uqYlxfNGCtr.',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:20',NULL,NULL),(14,'Staff Perpustakaan','staff.perpus@maisba.sch.id',NULL,NULL,NULL,'$2y$12$9ZaPmOwjigHDMxMSLXstKO9sktyq2cQveDr1nk9Rt1oHIWbfCPRLy',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:20',NULL,NULL),(15,'Admin Absensi','admin.absensi@maisba.sch.id',NULL,NULL,NULL,'$2y$12$voGFKC0Mb3EPvQM.tPBpR.VcG2WTbBbA/0WMPy9cvq4dTd83PbeX2',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:20',NULL,NULL),(16,'Staff Absensi','staff.absensi@maisba.sch.id',NULL,NULL,NULL,'$2y$12$6SRwJHSEOuE8HNxXYrb0Pu45D1xNuJ.koZYZF/8NR2HsjwWzBBqc2',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:20',NULL,NULL),(17,'Guru Demo','guru@maisba.sch.id',NULL,NULL,NULL,'$2y$12$E.4uNDX.nK6PdkQfQ5WXremup0U82uvF76c4yw8KhAZKKFMKSogiO',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:21','App\\Models\\Guru',3),(18,'Wali Kelas Demo','walikelas@maisba.sch.id',NULL,NULL,NULL,'$2y$12$HIvKkRq0YdTuvgw7LerMQuxmDa9Ia85GaEKckyHzxpvMWkN1.fTaK',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:22','App\\Models\\Guru',1),(19,'Siswa Demo','siswa@maisba.sch.id',NULL,NULL,NULL,'$2y$12$o/1ZkdLZkVyymGWS9iAV2.1tdKebU3jxm5GkuoSg/V6IAH0hTqaUa',NULL,'2026-10-30 09:19:37','2026-11-02 14:57:22','App\\Models\\Siswa',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wa_settings`
--

DROP TABLE IF EXISTS `wa_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `wa_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_proses_alpa` time DEFAULT NULL,
  `tipe_proses_alpa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Otomatis',
  `notif_wa_wali_masuk` tinyint(1) NOT NULL DEFAULT '1',
  `notif_wa_wali_pulang` tinyint(1) NOT NULL DEFAULT '1',
  `notif_wa_wali_alpa` tinyint(1) NOT NULL DEFAULT '1',
  `notif_wa_guru_masuk` tinyint(1) NOT NULL DEFAULT '1',
  `notif_wa_guru_pulang` tinyint(1) NOT NULL DEFAULT '1',
  `report_guru_alpa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wa_settings`
--

LOCK TABLES `wa_settings` WRITE;
/*!40000 ALTER TABLE `wa_settings` DISABLE KEYS */;
INSERT INTO `wa_settings` VALUES (1,'wWnexOmsVYfttNechAscSgNONWihTiKmuoYYrnaUWYkZnTuUob',NULL,'Manual',1,1,1,1,1,1,'2026-09-04 22:55:50','2026-10-23 08:48:04');
/*!40000 ALTER TABLE `wa_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-02 21:57:53
