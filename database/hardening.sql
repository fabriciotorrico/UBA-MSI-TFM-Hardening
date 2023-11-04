-- phpMyAdmin SQL Dump
-- version 4.6.6deb5ubuntu0.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 04, 2023 at 05:51 PM
-- Server version: 5.7.42-0ubuntu0.18.04.1
-- PHP Version: 7.2.24-0ubuntu0.18.04.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hardening`
--

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `direccion_ip` varchar(15) NOT NULL,
  `usuario` text,
  `contrasena` text,
  `id_usuario_updated` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `clientes_perfiles`
--

CREATE TABLE `clientes_perfiles` (
  `id_cliente_perfil` int(11) NOT NULL,
  `id_cliente` int(10) UNSIGNED NOT NULL,
  `id_perfil` int(10) UNSIGNED NOT NULL,
  `id_usuario_updated` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `escaneos`
--

CREATE TABLE `escaneos` (
  `id_escaneo` int(11) NOT NULL,
  `id_cliente_perfil` int(10) UNSIGNED NOT NULL,
  `notas` text,
  `ruta_archivo_xml` text NOT NULL,
  `ruta_archivo_html` text NOT NULL,
  `timestamp_escaneo` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `result_id` text,
  `ruta_archivo_hardening` text,
  `hardening_ejecutado` int(1) DEFAULT '0',
  `timestamp_ultimo_hardening` timestamp NULL DEFAULT NULL,
  `resultado_hardening` text,
  `id_usuario_updated` int(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `perfiles`
--

CREATE TABLE `perfiles` (
  `id_perfil` int(11) NOT NULL,
  `id_politica` int(10) UNSIGNED DEFAULT NULL,
  `profile_id` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `tipo` varchar(10) NOT NULL,
  `id_politica_base` int(11) DEFAULT NULL,
  `id_usuario_updated` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `perfiles_reglas`
--

CREATE TABLE `perfiles_reglas` (
  `id_perfil_regla` int(11) NOT NULL,
  `id_perfil` int(10) UNSIGNED NOT NULL,
  `id_regla` int(10) UNSIGNED NOT NULL,
  `habilitada` int(1) NOT NULL,
  `id_usuario_updated` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MenuVerConfiguracionesIniciales', 'menu_ver_configuraciones_iniciales', 'Ver el Menú de Configuraciones Iniciales (solo para vistas)', NULL, NULL),
(2, 'GestionDePersonas', 'gestion_de_personas', 'Gestionar datos, altas y bajas de personas', NULL, NULL),
(3, 'GestionDeUsuarios', 'gestion_de_usuarios', 'Gestion de usuarios', NULL, NULL),
(4, 'GestionDeRoles', 'gestion_de_roles', 'Gesion de roles de los usuarios (para controlar la asignación de roles)', NULL, NULL),
(5, 'Hardening', 'hardening', 'Gestionar todo lo relacionado con hardening', NULL, NULL),
(6, 'SoloVisualizar', 'solo_visualizar', 'Solo opciones de visualizacion', NULL, NULL),
(7, 'MenuVerTareasRecurrentes', 'menu_ver_tareas_recurrentes', 'Para ver o no el menu de tareas recurrentes (solo para vistas)', '2023-11-04 18:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`id`, `permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 1, NULL, NULL),
(3, 3, 1, NULL, NULL),
(4, 4, 1, NULL, NULL),
(5, 5, 1, NULL, NULL),
(6, 6, 1, NULL, NULL),
(12, 1, 2, NULL, NULL),
(13, 2, 2, NULL, NULL),
(14, 3, 2, NULL, NULL),
(15, 4, 2, NULL, NULL),
(16, 5, 2, NULL, NULL),
(25, 7, 2, NULL, NULL),
(34, 6, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personas`
--

CREATE TABLE `personas` (
  `id_persona` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `paterno` varchar(30) DEFAULT NULL,
  `materno` varchar(30) DEFAULT NULL,
  `cedula_identidad` int(10) NOT NULL,
  `complemento_cedula` varchar(5) DEFAULT NULL,
  `expedido` varchar(5) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono_celular` varchar(80) DEFAULT NULL,
  `telefono_referencia` varchar(40) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `fecha_registro` date NOT NULL,
  `id_responsable_registro` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `personas`
--

INSERT INTO `personas` (`id_persona`, `nombre`, `paterno`, `materno`, `cedula_identidad`, `complemento_cedula`, `expedido`, `fecha_nacimiento`, `telefono_celular`, `telefono_referencia`, `email`, `direccion`, `fecha_registro`, `id_responsable_registro`, `created_at`, `updated_at`, `activo`) VALUES
(1, 'Admin Sistema', ' ', ' ', 0, '', '', NULL, '0', NULL, NULL, NULL, '2021-04-10', 1, NULL, NULL, 1),
(55, 'Fabricio Gabriel', 'Torrico', 'Barahona', 6180245, '', 'LP', '1995-03-24', '60104841', NULL, NULL, NULL, '0000-00-00', 2, NULL, '2023-11-04 19:46:33', 1),
(289, 'Auditor', 'Externo', 'Uba', 1111111, '', 'LP', '2000-01-01', '1234567', '0', '', '', '2023-11-04', 2, '2023-11-04 19:47:36', '2023-11-04 19:47:36', 1);

-- --------------------------------------------------------

--
-- Table structure for table `politicas`
--

CREATE TABLE `politicas` (
  `id_politica` int(11) NOT NULL,
  `ruta_archivo` text NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `tipo_politica` varchar(10) DEFAULT NULL,
  `nombre` text NOT NULL,
  `descripcion` text NOT NULL,
  `id_usuario_updated` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `activo` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reglas`
--

CREATE TABLE `reglas` (
  `id_regla` int(10) UNSIGNED NOT NULL,
  `id_politica` int(10) UNSIGNED NOT NULL,
  `type` varchar(10) NOT NULL,
  `id_elemento` text NOT NULL,
  `title` text NOT NULL,
  `description` text,
  `id_regla_padre` int(11) NOT NULL DEFAULT '0',
  `id_usuario_updated` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `nivel` int(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `special` enum('all-access','no-access') COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `nivel`, `created_at`, `updated_at`, `special`, `estado`) VALUES
(1, 'SuperAdministrador', 'super_admin', 'Administrador del Sistema', 0, '2019-02-04 09:13:33', '2019-02-04 09:13:33', NULL, 0),
(2, 'Administrador', 'admin', 'Administrador de Sistemas / Infraestructura', 0, '2019-02-04 09:14:04', '2019-02-04 09:14:04', NULL, 1),
(3, 'Auditor', 'auditor', 'Auditor / Personal de Seguridad', 0, '2021-03-28 07:00:00', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL),
(156, 3, 144, '2023-11-04 20:50:49', '2023-11-04 20:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_persona` int(11) NOT NULL,
  `activo` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `id_persona`, `activo`) VALUES
(1, 'admin', '', 'c88c4af3200f350c8185b441babd8391', 'ytFJXhdHQjr3VgVqJUik0cb2EtZWFLJdZUnk8AqF2QMnkvqL95u8tiV3k8WJ', NULL, '2021-06-01 21:13:41', 1, 1),
(2, 'fabricio.torrico', '', '$2y$10$y6ZWSByla528Gyu0tOugB.nrGzaJu.YBgNqa9Gi2qkGBqbpyEDLvG', 'iVl9hNbFVNtVmBN1UHhQgLZUHN7k6BqiJzEJHl5AgkMX5XTxpXeqykKPmp9K', '2021-04-11 03:12:17', '2023-11-04 20:42:00', 55, 1),
(144, 'auditor.externo', '', '$2y$10$ADEhW5hjsvFLn8ocAJPCn.DYel8xwo4W3offRB0OXJZBX6nLWd2/C', '1JBNBv8m1PMUd1eCYJUvamk6WoZMS598XcV1TUf1FhFwny9pUGjE5HsBZAha', '2023-11-04 19:48:14', '2023-11-04 20:50:17', 289, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indexes for table `clientes_perfiles`
--
ALTER TABLE `clientes_perfiles`
  ADD PRIMARY KEY (`id_cliente_perfil`);

--
-- Indexes for table `escaneos`
--
ALTER TABLE `escaneos`
  ADD PRIMARY KEY (`id_escaneo`);

--
-- Indexes for table `perfiles`
--
ALTER TABLE `perfiles`
  ADD PRIMARY KEY (`id_perfil`);

--
-- Indexes for table `perfiles_reglas`
--
ALTER TABLE `perfiles_reglas`
  ADD PRIMARY KEY (`id_perfil_regla`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_role_permission_id_index` (`permission_id`),
  ADD KEY `permission_role_role_id_index` (`role_id`);

--
-- Indexes for table `personas`
--
ALTER TABLE `personas`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indexes for table `politicas`
--
ALTER TABLE `politicas`
  ADD PRIMARY KEY (`id_politica`);

--
-- Indexes for table `reglas`
--
ALTER TABLE `reglas`
  ADD PRIMARY KEY (`id_regla`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_user_role_id_index` (`role_id`),
  ADD KEY `role_user_user_id_index` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `clientes_perfiles`
--
ALTER TABLE `clientes_perfiles`
  MODIFY `id_cliente_perfil` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `escaneos`
--
ALTER TABLE `escaneos`
  MODIFY `id_escaneo` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `perfiles`
--
ALTER TABLE `perfiles`
  MODIFY `id_perfil` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `perfiles_reglas`
--
ALTER TABLE `perfiles_reglas`
  MODIFY `id_perfil_regla` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT for table `permission_role`
--
ALTER TABLE `permission_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
--
-- AUTO_INCREMENT for table `personas`
--
ALTER TABLE `personas`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=290;
--
-- AUTO_INCREMENT for table `politicas`
--
ALTER TABLE `politicas`
  MODIFY `id_politica` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `reglas`
--
ALTER TABLE `reglas`
  MODIFY `id_regla` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
