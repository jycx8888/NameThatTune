-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 08:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `namethattune`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `DateJoined` date DEFAULT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `Username`, `Password`, `DateJoined`, `ProfilePicture`) VALUES
('A001', 'mervin', 'Mervin&6969', '2025-01-09', 'uploads/mervin.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `admin_quiz`
--

CREATE TABLE `admin_quiz` (
  `AdminID` varchar(255) NOT NULL,
  `QuizID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_quiz`
--

INSERT INTO `admin_quiz` (`AdminID`, `QuizID`) VALUES
('A001', 'Q001'),
('A001', 'Q002'),
('A001', 'Q003'),
('A001', 'Q004'),
('A001', 'Q005'),
('A001', 'Q006'),
('A001', 'Q007'),
('A001', 'Q008'),
('A001', 'Q009'),
('A001', 'Q010');

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `GenreID` varchar(255) NOT NULL,
  `GenreName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`GenreID`, `GenreName`) VALUES
('G001', 'English'),
('G002', 'Japanese'),
('G003', 'Korean');

-- --------------------------------------------------------

--
-- Table structure for table `option`
--

CREATE TABLE `option` (
  `OptionID` varchar(255) NOT NULL,
  `OptionName` varchar(255) DEFAULT NULL,
  `QuestionID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `option`
--

INSERT INTO `option` (`OptionID`, `OptionName`, `QuestionID`) VALUES
('O001', 'See You Again', 'T001'),
('O002', 'Blinding Lights', 'T001'),
('O003', 'Darkside', 'T001'),
('O004', 'Poker Face', 'T001'),
('O005', 'Let Her Go', 'T002'),
('O006', 'Bye Bye Bye', 'T002'),
('O007', 'That\'s What I Like', 'T002'),
('O008', 'Wake Me Up', 'T002'),
('O009', 'Blank Space', 'T003'),
('O010', 'Light Switch', 'T003'),
('O011', 'Hall Of Fame', 'T003'),
('O012', 'Natural', 'T003'),
('O013', 'All of Me', 'T004'),
('O014', 'Ghost', 'T004'),
('O015', 'The Nights', 'T004'),
('O016', 'Better Now', 'T004'),
('O017', 'I\'m Yours', 'T005'),
('O018', 'All The Stars', 'T005'),
('O019', 'HOPE', 'T005'),
('O020', 'Starboy', 'T005'),
('O021', 'Payphone', 'T006'),
('O022', 'Night Changes', 'T006'),
('O023', 'Talking To The Moon', 'T006'),
('O024', 'Wolves', 'T006'),
('O025', 'Counting Stars', 'T007'),
('O026', 'Bad Liar', 'T007'),
('O027', 'When I Was Your Man', 'T007'),
('O028', 'As It Was', 'T007'),
('O029', 'Bad Romance', 'T008'),
('O030', '24k Magic', 'T008'),
('O031', 'Drivers License', 'T008'),
('O032', 'Stay', 'T008'),
('O033', 'We Don\'t Talk Anymore', 'T009'),
('O034', 'Bad Habits', 'T009'),
('O035', 'Happier Than Ever', 'T009'),
('O036', 'Shallow', 'T009'),
('O037', 'Treat You Better', 'T010'),
('O038', 'Levitating', 'T010'),
('O039', 'Someone Like You', 'T010'),
('O040', 'Sunflower', 'T010'),
('O041', 'Viva La Vida', 'T011'),
('O042', 'Self Love', 'T011'),
('O043', 'Cold', 'T011'),
('O044', 'Unstoppable ', 'T011'),
('O045', 'Calling', 'T012'),
('O046', 'Peaches', 'T012'),
('O047', 'Perfect', 'T012'),
('O048', 'Sorry', 'T012'),
('O049', 'Rewrite The Stars', 'T013'),
('O050', 'Clocks', 'T013'),
('O051', 'Hello', 'T013'),
('O052', 'We Will Rock You', 'T013'),
('O053', 'Doja', 'T014'),
('O054', 'Closer', 'T014'),
('O055', 'Faded', 'T014'),
('O056', 'Rockstar', 'T014'),
('O057', 'Humble.', 'T015'),
('O058', 'Hotel California', 'T015'),
('O059', 'Dusk Till Dawn', 'T015'),
('O060', 'Timber', 'T015'),
('O061', 'Mixed Nuts', 'T016'),
('O062', 'First Love', 'T016'),
('O063', 'Jidai', 'T016'),
('O064', 'Heavy Rotation', 'T016'),
('O065', 'Lemon', 'T017'),
('O066', 'Sparkle', 'T017'),
('O067', 'Can You Celebrate', 'T017'),
('O068', 'My Dearest', 'T017'),
('O069', 'Gurenge', 'T018'),
('O070', 'PONPONPON', 'T018'),
('O071', 'Sorairo Days', 'T018'),
('O072', 'Polyrhythm', 'T018'),
('O073', 'Koi', 'T019'),
('O074', 'Rising Hope', 'T019'),
('O075', 'Best Friend', 'T019'),
('O076', 'Odoru Ponpokorin', 'T019'),
('O077', 'Pretender', 'T020'),
('O078', 'Sekai wa Koi ni Ochiteiru', 'T020'),
('O079', 'Kaze wa Fuiteiru', 'T020'),
('O080', 'Every Heart', 'T020'),
('O081', 'CRY FOR ME', 'T021'),
('O082', 'Kimagure Romantic', 'T021'),
('O083', 'Piano Man', 'T021'),
('O084', 'Orion', 'T021'),
('O085', 'Shinunoga E-Wa', 'T022'),
('O086', 'TOMORROW', 'T022'),
('O087', 'Plastic Love', 'T022'),
('O088', 'Ride on Time', 'T022'),
('O089', 'BLUE BIRD', 'T023'),
('O090', 'Stay With Me', 'T023'),
('O091', 'Shiki no Uta', 'T023'),
('O092', 'Little Glee Monster', 'T023'),
('O093', 'Unravel', 'T024'),
('O094', 'Aitakatta', 'T024'),
('O095', 'Gekkou', 'T024'),
('O096', 'Sakura', 'T024'),
('O097', 'Zenzenzense', 'T025'),
('O098', 'For You', 'T025'),
('O099', 'Orange', 'T025'),
('O100', 'Koi wa Sensou', 'T025'),
('O101', 'Silhouette', 'T026'),
('O102', 'Fireworks', 'T026'),
('O103', 'Yume wo Kanaete Doraemon', 'T026'),
('O104', 'Kawaranai Mono', 'T026'),
('O105', 'Flamingo', 'T027'),
('O106', 'Hikaru Nara', 'T027'),
('O107', 'Butterfly', 'T027'),
('O108', 'Hero', 'T027'),
('O109', 'Homura', 'T028'),
('O110', 'A Cruel Angel\'s Thesis', 'T028'),
('O111', 'Moonlight Densetsu', 'T028'),
('O112', 'Dreamin\' On', 'T028'),
('O113', 'Kaikaikitan', 'T029'),
('O114', 'Life Goes On', 'T029'),
('O115', 'Stay', 'T029'),
('O116', 'Dearest', 'T029'),
('O117', 'Gunjou', 'T030'),
('O118', 'Eikou no Kakehashi', 'T030'),
('O119', 'RPG', 'T030'),
('O120', 'No.1', 'T030'),
('O121', 'Tomboy', 'T031'),
('O122', 'Butter', 'T031'),
('O123', 'Dynamite', 'T031'),
('O124', 'Permission to Dance', 'T031'),
('O125', 'Supernova', 'T032'),
('O126', 'Spring Day', 'T032'),
('O127', 'Fake Love', 'T032'),
('O128', 'TT', 'T032'),
('O129', 'FOREVER', 'T033'),
('O130', 'What is Love', 'T033'),
('O131', 'Fancy', 'T033'),
('O132', 'Cheer Up', 'T033'),
('O133', 'I GOT YOU', 'T034'),
('O134', 'POP!', 'T034'),
('O135', 'Feel My Rhythm', 'T034'),
('O136', 'Psycho', 'T034'),
('O137', 'SHEESH', 'T035'),
('O138', 'Red Flavor', 'T035'),
('O139', 'Love Dive', 'T035'),
('O140', 'After LIKE', 'T035'),
('O141', 'Trouble Maker', 'T036'),
('O142', 'Eleven', 'T036'),
('O143', 'Nxde', 'T036'),
('O144', 'TOMBOY', 'T036'),
('O145', 'Super Shy', 'T037'),
('O146', 'Queencard', 'T037'),
('O147', 'ZOOM', 'T037'),
('O148', 'That That', 'T037'),
('O149', 'Loser', 'T038'),
('O150', 'Gentleman', 'T038'),
('O151', 'Gangnam Style', 'T038'),
('O152', 'VIBE', 'T038'),
('O153', 'APT', 'T039'),
('O154', 'Eyes, Nose, Lips', 'T039'),
('O155', 'Hype Boy', 'T039'),
('O156', 'Attention', 'T039'),
('O157', 'How You Like That', 'T040'),
('O158', 'OMG', 'T040'),
('O159', 'Antifragile', 'T040'),
('O160', 'FEARLESS', 'T040'),
('O161', 'Armageddon', 'T041'),
('O162', 'Stay Alive', 'T041'),
('O163', 'Euphoria', 'T041'),
('O164', 'Daechwita', 'T041'),
('O165', 'DASH', 'T042'),
('O166', 'Blue & Grey', 'T042'),
('O167', 'Stray Kids', 'T042'),
('O168', 'Thunderous', 'T042'),
('O169', 'Magnetic', 'T043'),
('O170', 'God\'s Menu', 'T043'),
('O171', 'Maniac', 'T043'),
('O172', 'Runaway', 'T043'),
('O173', 'Ditto', 'T044'),
('O174', 'CROWN', 'T044'),
('O175', 'Blue Hour', 'T044'),
('O176', 'Drunk-Dazed', 'T044'),
('O177', 'Hype Boy', 'T045'),
('O178', 'Given-Taken', 'T045'),
('O179', 'Polaroid Love', 'T045'),
('O180', 'Can\'t You See Me?', 'T045'),
('O181', 'Sugar', 'T046'),
('O182', 'w', 'T046'),
('O183', 'e', 'T046'),
('O184', 'w', 'T046'),
('O185', 'Sugar', 'T047'),
('O186', 'e', 'T047'),
('O187', 'r', 'T047'),
('O188', 'e', 'T047'),
('O189', 'Sugar', 'T048'),
('O190', 's', 'T048'),
('O191', 'a', 'T048'),
('O192', 's', 'T048'),
('O193', 's', 'T049'),
('O194', 's', 'T049'),
('O195', 'Sugar', 'T049'),
('O196', '3', 'T049'),
('O197', 'e', 'T050'),
('O198', 'r', 'T050'),
('O199', 'e', 'T050'),
('O200', 'Sugar', 'T050');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `QuestionID` varchar(255) NOT NULL,
  `CorrectRate` float DEFAULT NULL,
  `QuizID` varchar(255) DEFAULT NULL,
  `CorrectAnswer` varchar(255) DEFAULT NULL,
  `TotalAttempts` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`QuestionID`, `CorrectRate`, `QuizID`, `CorrectAnswer`, `TotalAttempts`) VALUES
('T001', 0.5, 'Q001', 'See You Again', 2),
('T002', 1, 'Q001', 'Let Her Go', 2),
('T003', 1, 'Q001', 'Blank Space', 2),
('T004', 1, 'Q001', 'All of Me', 2),
('T005', 1, 'Q001', 'I\'m Yours', 2),
('T006', 1, 'Q002', 'Payphone', 1),
('T007', 1, 'Q002', 'Counting Stars', 1),
('T008', 1, 'Q002', 'Bad Romance', 1),
('T009', 0, 'Q002', 'We Don\'t Talk Anymore', 1),
('T010', 0, 'Q002', 'Treat You Better', 1),
('T011', 0, 'Q003', 'Viva La Vida', 0),
('T012', 0, 'Q003', 'Calling', 0),
('T013', 0, 'Q003', 'Rewrite The Stars', 0),
('T014', 0, 'Q003', 'Doja', 0),
('T015', 0, 'Q003', 'Humble.', 0),
('T016', 1, 'Q004', 'Mixed Nuts', 1),
('T017', 1, 'Q004', 'Lemon', 1),
('T018', 0, 'Q004', 'Gurenge', 1),
('T019', 1, 'Q004', 'Koi', 1),
('T020', 0, 'Q004', 'Pretender', 1),
('T021', 0, 'Q005', 'CRY FOR ME', 0),
('T022', 0, 'Q005', 'Shinunoga E-Wa', 0),
('T023', 0, 'Q005', 'BLUE BIRD', 0),
('T024', 0, 'Q005', 'Unravel', 0),
('T025', 0, 'Q005', 'Zenzenzense', 0),
('T026', 1, 'Q006', 'Silhouette', 1),
('T027', 1, 'Q006', 'Flamingo', 1),
('T028', 1, 'Q006', 'Homura', 1),
('T029', 1, 'Q006', 'Kaikaikitan', 1),
('T030', 1, 'Q006', 'Gunjou', 1),
('T031', 1, 'Q007', 'Tomboy', 2),
('T032', 1, 'Q007', 'Supernova', 2),
('T033', 1, 'Q007', 'FOREVER', 2),
('T034', 1, 'Q007', 'I GOT YOU', 2),
('T035', 1, 'Q007', 'SHEESH', 2),
('T036', 0, 'Q008', 'Trouble Maker', 0),
('T037', 0, 'Q008', 'Super Shy', 0),
('T038', 0, 'Q008', 'Loser', 0),
('T039', 0, 'Q008', 'APT', 0),
('T040', 0, 'Q008', 'How You Like That', 0),
('T041', 0, 'Q009', 'Armageddon', 0),
('T042', 0, 'Q009', 'DASH', 0),
('T043', 0, 'Q009', 'Magnetic', 0),
('T044', 0, 'Q009', 'Ditto', 0),
('T045', 0, 'Q009', 'Hype Boy', 0),
('T046', 0, 'Q010', 'Sugar', 0),
('T047', 0, 'Q010', 'Sugar', 0),
('T048', 0, 'Q010', 'Sugar', 0),
('T049', 0, 'Q010', 'Sugar', 0),
('T050', 0, 'Q010', 'Sugar', 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `QuizID` varchar(255) NOT NULL,
  `GenreID` varchar(255) DEFAULT NULL,
  `CreatedTime` datetime DEFAULT NULL,
  `QuizName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`QuizID`, `GenreID`, `CreatedTime`, `QuizName`) VALUES
('Q001', 'G001', '2001-12-15 12:04:35', 'English Quiz 1'),
('Q002', 'G001', '2001-12-15 12:07:15', 'English Quiz 2'),
('Q003', 'G001', '2001-12-15 12:11:42', 'English Quiz 3'),
('Q004', 'G002', '2001-12-15 12:15:44', 'Japanese Quiz 1'),
('Q005', 'G002', '2001-12-15 12:17:33', 'Japanese Quiz 2'),
('Q006', 'G002', '2001-12-15 12:15:20', 'Japanese Quiz 3'),
('Q007', 'G003', '2001-12-15 01:00:59', 'Korean Quiz 1'),
('Q008', 'G003', '2001-12-15 04:00:58', 'Korean Quiz 2'),
('Q009', 'G003', '2001-12-15 04:00:32', 'Korean Quiz 3'),
('Q010', 'G001', '2025-02-25 06:15:33', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

CREATE TABLE `record` (
  `RecordID` varchar(255) NOT NULL,
  `Result` varchar(255) DEFAULT NULL,
  `Time` datetime DEFAULT NULL,
  `UserID` varchar(255) DEFAULT NULL,
  `QuizID` varchar(255) DEFAULT NULL,
  `TimeUsed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `record`
--

INSERT INTO `record` (`RecordID`, `Result`, `Time`, `UserID`, `QuizID`, `TimeUsed`) VALUES
('R001', '3', '2025-02-15 15:01:41', 'U001', 'Q004', 37),
('R002', '4', '2025-02-17 06:55:02', 'U001', 'Q001', 35),
('R003', '5', '2025-02-20 09:28:01', 'U001', 'Q007', 26),
('R004', '5', '2025-02-20 13:35:55', 'U001', 'Q006', 457),
('R005', '5', '2025-02-22 17:38:55', 'U001', 'Q007', 14),
('R006', '3', '2025-02-24 22:11:28', 'U001', 'Q002', 10),
('R007', '5', '2025-06-16 12:20:16', 'U001', 'Q001', 16);

-- --------------------------------------------------------

--
-- Table structure for table `record_question`
--

CREATE TABLE `record_question` (
  `RecordID` varchar(255) NOT NULL,
  `QuestionID` varchar(255) NOT NULL,
  `UserAnswer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `record_question`
--

INSERT INTO `record_question` (`RecordID`, `QuestionID`, `UserAnswer`) VALUES
('R001', 'T016', 'Mixed Nuts'),
('R001', 'T017', 'Lemon'),
('R001', 'T018', 'PONPONPON'),
('R001', 'T019', 'Koi'),
('R001', 'T020', 'Sekai wa Koi ni Ochiteiru'),
('R002', 'T001', 'Poker Face'),
('R002', 'T002', 'Let Her Go'),
('R002', 'T003', 'Blank Space'),
('R002', 'T004', 'All of Me'),
('R002', 'T005', 'I\'m Yours'),
('R003', 'T031', 'Tomboy'),
('R003', 'T032', 'Supernova'),
('R003', 'T033', 'FOREVER'),
('R003', 'T034', 'I GOT YOU'),
('R003', 'T035', 'SHEESH'),
('R004', 'T026', 'Silhouette'),
('R004', 'T027', 'Flamingo'),
('R004', 'T028', 'Homura'),
('R004', 'T029', 'Kaikaikitan'),
('R004', 'T030', 'Gunjou'),
('R005', 'T031', 'Tomboy'),
('R005', 'T032', 'Supernova'),
('R005', 'T033', 'FOREVER'),
('R005', 'T034', 'I GOT YOU'),
('R005', 'T035', 'SHEESH'),
('R006', 'T006', 'Payphone'),
('R006', 'T007', 'Counting Stars'),
('R006', 'T008', 'Bad Romance'),
('R006', 'T009', 'Shallow'),
('R006', 'T010', 'Levitating'),
('R007', 'T001', 'See You Again'),
('R007', 'T002', 'Let Her Go'),
('R007', 'T003', 'Blank Space'),
('R007', 'T004', 'All of Me'),
('R007', 'T005', 'I\'m Yours');

-- --------------------------------------------------------

--
-- Table structure for table `song`
--

CREATE TABLE `song` (
  `SongID` varchar(255) NOT NULL,
  `SongName` varchar(255) DEFAULT NULL,
  `QuestionID` varchar(255) DEFAULT NULL,
  `SongAudio` varchar(255) DEFAULT NULL,
  `SongImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `song`
--

INSERT INTO `song` (`SongID`, `SongName`, `QuestionID`, `SongAudio`, `SongImage`) VALUES
('S001', 'See You Again', 'T001', 'Question Songs/See You Again.MP3', 'Question Images/See You Again-min.jpg'),
('S002', 'Let Her Go', 'T002', 'Question Songs/Let Her Go.MP3', 'Question Images/Let Her Go-min.jpg'),
('S003', 'Blank Space', 'T003', 'Question Songs/Blank Space.MP3', 'Question Images/Blank Space-min.jpg'),
('S004', 'All of Me', 'T004', 'Question Songs/All Of Me.MP3', 'Question Images/All Of Me-min.jpg'),
('S005', 'I\'m Yours', 'T005', 'Question Songs/I\'m Yours.MP3', 'Question Images/I\'m Yours-min.png'),
('S006', 'Payphone', 'T006', 'Question Songs/Payphone.MP3', 'Question Images/Payphone.jpg'),
('S007', 'Counting Stars', 'T007', 'Question Songs/Counting Stars.MP3', 'Question Images/Counting Stars-min.png'),
('S008', 'Bad Romance', 'T008', 'Question Songs/Bad Romance.MP3', 'Question Images/Bad Romance-min.png'),
('S009', 'We Don\'t Talk Anymore', 'T009', 'Question Songs/We Don\'t Talk Anymore.MP3', 'Question Images/We Dont Talk Anymore-min.jpg'),
('S010', 'Treat You Better', 'T010', 'Question Songs/Treat You Better.MP3', 'Question Images/Treat You Better-min.png'),
('S011', 'Viva La Vida', 'T011', 'Question Songs/Viva La Vida.MP3', 'Question Images/Viva La Vida-min.jpg'),
('S012', 'Calling', 'T012', 'Question Songs/Calling.MP3', 'Question Images/Calling-min.jpg'),
('S013', 'Rewrite The Stars', 'T013', 'Question Songs/Rewrite The Stars.MP3', 'Question Images/Rewrite The Stars-min.jpg'),
('S014', 'Doja', 'T014', 'Question Songs/Doja.MP3', 'Question Images/Doja-min.jpg'),
('S015', 'HUMBLE.', 'T015', 'Question Songs/Humble..MP3', 'Question Images/Humble.-min.jpg'),
('S016', 'Mixed Nuts', 'T016', 'Question Songs/Mixed Nuts.MP3', 'Question Images/Mixed Nuts.jpeg'),
('S017', 'Lemon', 'T017', 'Question Songs/Lemon.MP3', 'Question Images/Lemon.jpeg'),
('S018', 'Gurenge', 'T018', 'Question Songs/Gurenge.MP3', 'Question Images/Gurenge.jpg'),
('S019', 'Koi', 'T019', 'Question Songs/Koi.MP3', 'Question Images/KOI.jpeg'),
('S020', 'Pretender', 'T020', 'Question Songs/Pretender.MP3', 'Question Images/Pretender.png'),
('S021', 'CRY FOR ME', 'T021', 'Question Songs/CRY FOR ME.MP3', 'Question Images/CRY FOR ME.jpeg'),
('S022', 'Shinunoga E-Wa', 'T022', 'Question Songs/Shinunoga E-Wa.MP3', 'Question Images/Shinunoga E-Wa.jpg'),
('S023', 'BLUE BIRD', 'T023', 'Question Songs/BLUE BIRD.MP3', 'Question Images/BLUE BIRD.jpg'),
('S024', 'Unravel', 'T024', 'Question Songs/Unravel.MP3', 'Question Images/Unravel.jpeg'),
('S025', 'Zenzenzense', 'T025', 'Question Songs/Zenzenzense.MP3', 'Question Images/Zenzenzense.jpg'),
('S026', 'Silhouette', 'T026', 'Question Songs/Silhouette.MP3', 'Question Images/Silhouette.jpeg'),
('S027', 'Flamingo', 'T027', 'Question Songs/Flamingo.MP3', 'Question Images/Flamingo.png'),
('S028', 'Homura', 'T028', 'Question Songs/Homura.MP3', 'Question Images/Hamura.jpeg'),
('S029', 'Kaikaikitan', 'T029', 'Question Songs/Kaikaikitan.MP3', 'Question Images/Kaikaikitan.jpeg'),
('S030', 'Gunjo', 'T030', 'Question Songs/Gunjo.MP3', 'Question Images/Gunjo.jpeg'),
('S031', 'Tomboy', 'T031', 'Question Songs/Tomboy.MP3', 'Question Images/Tomboy.jpg'),
('S032', 'Supernova', 'T032', 'Question Songs/Supernova.MP3', 'Question Images/Supernova.png'),
('S033', 'FOREVER', 'T033', 'Question Songs/Forever.MP3', 'Question Images/Forever.jpg'),
('S034', 'I GOT YOU', 'T034', 'Question Songs/I Got You.MP3', 'Question Images/I Got You.jpg'),
('S035', 'SHEESH', 'T035', 'Question Songs/Sheesh.MP3', 'Question Images/Sheesh.jpg'),
('S036', 'Trouble Maker', 'T036', 'Question Songs/Trouble Maker.MP3', 'Question Images/Trouble Maker.jpg'),
('S037', 'Super Shy', 'T037', 'Question Songs/Super Shy.MP3', 'Question Images/Super Shy.jpg'),
('S038', 'Loser', 'T038', 'Question Songs/Loser.MP3', 'Question Images/Loser.jpg'),
('S039', 'APT', 'T039', 'Question Songs/APT.MP3', 'Question Images/APT.png'),
('S040', 'How You Like That', 'T040', 'Question Songs/How you like that.MP3', 'Question Images/How You Like That.png'),
('S041', 'Armaggedon', 'T041', 'Question Songs/Armageddon.MP3', 'Question Images/Armageddon.jpg'),
('S042', 'DASH', 'T042', 'Question Songs/DASH.MP3', 'Question Images/Dash.jpg'),
('S043', 'Magnetic', 'T043', 'Question Songs/Magnetic.MP3', 'Question Images/Magnetic.png'),
('S044', 'Ditto', 'T044', 'Question Songs/Ditto.MP3', 'Question Images/Ditto.jpg'),
('S045', 'Hype Boy', 'T045', 'Question Songs/Hype Boy.MP3', 'Question Images/Hype Boy.jpg'),
('S046', 'Girls Like You', NULL, 'Question Songs/Maroon 5 - Girls Like You ft (mp3cut.net).mp3', 'Question Images/girls like you.jpg'),
('S047', 'Maps', NULL, 'Question Songs/Maroon 5 - Maps (mp3cut.net).mp3', 'Question Images/maps.jpg'),
('S048', 'Animals', NULL, 'Question Songs/Maroon 5 - Animals (mp3cut.net).mp3', 'Question Images/animal.jpg'),
('S049', 'One More Night', NULL, 'Question Songs/Maroon 5 - One More Night (Official Music Video) (mp3cut.net).mp3', 'Question Images/One more night.jpg'),
('S050', 'Sugar', 'T046', 'Question Songs/Maroon 5 - Sugar (Official Music Video) (mp3cut.net).mp3', 'Question Images/sugar.jpg'),
('S051', 'Sugar', 'T047', 'Question Songs/Maroon 5 - Sugar (Official Music Video) (mp3cut.net).mp3', 'Question Images/sugar.jpg'),
('S052', 'Sugar', 'T049', 'Question Songs/Maroon 5 - Sugar (Official Music Video) (mp3cut.net).mp3', 'Question Images/sugar.jpg'),
('S053', 'Sugar', 'T048', 'Question Songs/Maroon 5 - Sugar (Official Music Video) (mp3cut.net).mp3', 'Question Images/sugar.jpg'),
('S054', 'Sugar', 'T050', 'Question Songs/Maroon 5 - Sugar (Official Music Video) (mp3cut.net).mp3', 'Question Images/sugar.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `DateJoined` date DEFAULT NULL,
  `AnswerCorrectRate` float DEFAULT NULL,
  `ProfilePicture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `Username`, `Password`, `DateJoined`, `AnswerCorrectRate`, `ProfilePicture`) VALUES
('U001', 'Justinnn', 'Justin%69', '2025-02-10', 0, 'uploads/girl.jpg'),
('U002', 'yongjun', 'Yj%176969', '2025-01-12', 0, 'uploads/kum.jpg'),
('U003', 'mervin', 'Mervin1Sg@y', '2025-01-12', 0, 'uploads/mervin.jpg'),
('U004', 'hongyi', 'HongYi%1122', '2025-01-16', 0, 'uploads/hongyi.jpg'),
('U005', 'Joshua', 'Joshua$12', '2025-02-10', 0, 'uploads/joshua.jpg'),
('U006', 'mervin', 'Mervin!12', '2025-06-16', 0, 'uploads/avatar.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `admin_quiz`
--
ALTER TABLE `admin_quiz`
  ADD PRIMARY KEY (`AdminID`,`QuizID`),
  ADD KEY `QuizID` (`QuizID`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`GenreID`);

--
-- Indexes for table `option`
--
ALTER TABLE `option`
  ADD PRIMARY KEY (`OptionID`),
  ADD KEY `QuestionID` (`QuestionID`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`QuestionID`),
  ADD KEY `QuizID` (`QuizID`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`QuizID`),
  ADD KEY `GenreID` (`GenreID`);

--
-- Indexes for table `record`
--
ALTER TABLE `record`
  ADD PRIMARY KEY (`RecordID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `QuizID` (`QuizID`);

--
-- Indexes for table `record_question`
--
ALTER TABLE `record_question`
  ADD PRIMARY KEY (`RecordID`,`QuestionID`),
  ADD KEY `QuestionID` (`QuestionID`);

--
-- Indexes for table `song`
--
ALTER TABLE `song`
  ADD PRIMARY KEY (`SongID`),
  ADD KEY `QuestionID` (`QuestionID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_quiz`
--
ALTER TABLE `admin_quiz`
  ADD CONSTRAINT `admin_quiz_ibfk_1` FOREIGN KEY (`AdminID`) REFERENCES `admin` (`AdminID`),
  ADD CONSTRAINT `admin_quiz_ibfk_2` FOREIGN KEY (`QuizID`) REFERENCES `quiz` (`QuizID`);

--
-- Constraints for table `option`
--
ALTER TABLE `option`
  ADD CONSTRAINT `option_ibfk_1` FOREIGN KEY (`QuestionID`) REFERENCES `question` (`QuestionID`);

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`QuizID`) REFERENCES `quiz` (`QuizID`);

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`GenreID`) REFERENCES `genre` (`GenreID`);

--
-- Constraints for table `record`
--
ALTER TABLE `record`
  ADD CONSTRAINT `record_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `record_ibfk_2` FOREIGN KEY (`QuizID`) REFERENCES `quiz` (`QuizID`);

--
-- Constraints for table `record_question`
--
ALTER TABLE `record_question`
  ADD CONSTRAINT `record_question_ibfk_1` FOREIGN KEY (`RecordID`) REFERENCES `record` (`RecordID`),
  ADD CONSTRAINT `record_question_ibfk_2` FOREIGN KEY (`QuestionID`) REFERENCES `question` (`QuestionID`);

--
-- Constraints for table `song`
--
ALTER TABLE `song`
  ADD CONSTRAINT `song_ibfk_1` FOREIGN KEY (`QuestionID`) REFERENCES `question` (`QuestionID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
