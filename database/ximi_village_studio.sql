-- Table `about`

CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `quote_vi` varchar(255) NOT NULL,
  `quote_en` varchar(255) NOT NULL,
  `dream_job` varchar(100) NOT NULL,
  `coding_snack` varchar(100) NOT NULL,
  `hometown` varchar(100) NOT NULL,
  `part1_contribution` text NOT NULL,
  `part2_contribution` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert data `about`

INSERT INTO `about` (`id`, `student_id`, `full_name`, `quote_vi`, `quote_en`, `dream_job`, `coding_snack`, `hometown`, `part1_contribution`, `part2_contribution`) VALUES
(1, '106679202', 'Huỳnh Đỗ Trí Vĩ', 'Hành trình ngàn dặm bắt đầu bằng một bước chân.', 'A journey of a thousand miles begins with a single step.', 'AI Engineer', 'Iced Coffee', 'Ho Chi Minh City', 'Developed about.html and group info.', 'MySQL schema, seed SQL, DB/CRUD check, dynamic About page, slides & peer review.'),
(2, '106678034', 'Nguyễn Khang Thịnh', 'Mến thì đến', 'If we vibe, we vibe', 'Sleeper', 'Tea', 'Ho Chi Minh City', 'Designed index.html and landing layout.', 'Part 1 audit, PHP/index.php conversion, includes, settings/DB, integration & deployment checks.'),
(3, '106231246', 'Ngô Gia Phú Thịnh', 'Kiến thức là chìa khóa mở ra cánh cửa tương lai.', 'Knowledge is the key to unlocking the future.', 'Security Engineer', 'Matcha Cookies', 'Ho Chi Minh City', 'Developed jobs.html and job descriptions.', 'Dynamic jobs, search query, edge/security tests, submission package & demos.'),
(4, '106680000', 'Lý Bá Huy', 'Đừng đợi cơ hội đến, hãy tự tạo ra nó.', 'Don''t wait for opportunity. Create it.', 'Web Developer', 'Pepsi', 'Ho Chi Minh City', 'Developed apply.html and form rules.', 'EOI mapping, process_eoi validation, direct-access block, login/session & HR queries.');

-- Table `eoi`

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `job_reference` varchar(10) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `last_name` varchar(20) NOT NULL,
  `date_of_birth` varchar(10) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `street_address` varchar(40) NOT NULL,
  `suburb` varchar(40) NOT NULL,
  `state` varchar(10) NOT NULL,
  `postcode` varchar(4) NOT NULL,
  `email` varchar(254) NOT NULL,
  `phone` varchar(13) NOT NULL,
  `skills` varchar(255) DEFAULT '',
  `other_skills` text DEFAULT NULL,
  `status` enum('New','Current','Final') NOT NULL DEFAULT 'New'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert table `eoi`

INSERT INTO `eoi` (`EOInumber`, `job_reference`, `first_name`, `last_name`, `date_of_birth`, `gender`, `street_address`, `suburb`, `state`, `postcode`, `email`, `phone`, `skills`, `other_skills`, `status`) VALUES
(1, 'FE001', 'Son', 'Pham', '06/07/2007', 'Male', 'A67 Bach Dang', 'Tan Binh District', 'HCM', '7000', 'sonpham@eg.com', '0967676767', 'Teamwork, Communication', 'Good time management', 'New');

-- Table `jobs`

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `job_reference` varchar(10) NOT NULL,
  `title` varchar(100) NOT NULL,
  `salary` varchar(100) NOT NULL,
  `reports_to` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `responsibilities` text NOT NULL,
  `essential_requirements` text NOT NULL,
  `preferable_requirements` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert table `jobs`

INSERT INTO `jobs` (`id`, `job_reference`, `title`, `salary`, `reports_to`, `description`, `responsibilities`, `essential_requirements`, `preferable_requirements`) VALUES
(1, 'FE001', 'Front-End Web Developer', '$65,000 to $100,000 plus superannuation', 'Head of Development', 'Build responsive, accessible websites using HTML5 and CSS3.', 'Convert mockups to HTML/CSS; Follow WCAG guidelines; Maintain UI components; Collaborate in sprints.', 'HTML5, CSS3 (Flexbox/Grid); Accessibility & validation; Git/GitHub.', 'React or Vue; Jira or agile tools.'),
(2, 'UX002', 'UI/UX Designer', '$60,000 to $95,000 plus superannuation', 'Creative Director', 'Shape look, feel and usability of digital products.', 'Create wireframes/prototypes; Run usability testing; Maintain design standards; Present to clients.', 'Portfolio; Figma or Adobe XD; Colour contrast & layout.', 'Basic HTML/CSS; Gov or education projects.');


ALTER TABLE `about`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_reference` (`job_reference`);

ALTER TABLE `about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- Manager accounts
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;