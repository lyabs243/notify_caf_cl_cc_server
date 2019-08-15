-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 29, 2018 at 08:04 AM
-- Server version: 5.5.40-36.1-log
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `viavio7b_all_in_one_news_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(200) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id`, `username`, `password`, `email`, `image`) VALUES
(1, 'admin', 'admin', 'viaviwebtech@gmail.com', 'profile.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `cid` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`cid`, `category_name`) VALUES
(1, 'Sports'),
(2, 'Fashion'),
(3, 'Entertainment'),
(4, 'Politics'),
(6, 'Technologies'),
(7, 'Health'),
(8, 'World');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_channel`
--

CREATE TABLE `tbl_channel` (
  `id` int(11) NOT NULL,
  `channel_name` varchar(255) NOT NULL,
  `channel_url` varchar(255) NOT NULL,
  `channel_description` text NOT NULL,
  `channel_logo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_channel`
--

INSERT INTO `tbl_channel` (`id`, `channel_name`, `channel_url`, `channel_description`, `channel_logo`) VALUES
(1, 'Viavi Webtech TV', 'https://5b44cf20b0388.streamlock.net:8443/live/ngrp:live_all/playlist.m3u8', '<p>Viavi Webtech, India&rsquo;s most watched general English news channel is devoted to providing pure and relevant news to its viewers around the clock. Viavi Webtech has attained its leadership position by consistently delivering news to its viewers in a vivid and insightful manner, since its launch in January 2006. Besides India, Viavi Webtech also caters to the audiences of USA, Canada, Australia, New Zealand, Kenya, Tanzania, Uganda, Ethiopia and Nepal. Viavi Webtech is part of Times Television Network that comprises ET NOW, zoOm, MOVIES NOW &amp; Romedy NOW and caters to the affluent urban audience of India. Times Television Network is part of India&rsquo;s largest media conglomerate, The Times Group.</p>\r\n', 'TimesNow-w250.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comments`
--

CREATE TABLE `tbl_comments` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `comment_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

 

-- --------------------------------------------------------

--
-- Table structure for table `tbl_news`
--

CREATE TABLE `tbl_news` (
  `id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `news_type` varchar(255) NOT NULL,
  `news_heading` varchar(500) NOT NULL,
  `news_description` text NOT NULL,
  `news_featured_image` varchar(255) NOT NULL,
  `news_date` varchar(255) NOT NULL,
  `news_video_id` varchar(255) NOT NULL,
  `news_video_url` varchar(255) NOT NULL,
  `total_views` int(11) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_news`
--

INSERT INTO `tbl_news` (`id`, `cat_id`, `news_type`, `news_heading`, `news_description`, `news_featured_image`, `news_date`, `news_video_id`, `news_video_url`, `total_views`, `status`) VALUES
(7, 4, 'image', 'Presidential elections: Take decision in best interest of country, Meira Kumar to electorate', '<p>Named by the opposition as its joint candidate for the presidential poll, Meira Kumar today appealed to the electorate to take their decision in the best interest of the country based on cherished values of social justice and inclusiveness and on principles and ideologies.&nbsp;</p>\r\n\r\n<p>She also thanked the 17 opposition parties for supporting her candidature and said she is delighted by the opposition unity that represents the coming together of forces which have a strong ideological base.&nbsp;</p>\r\n\r\n<p>&quot;I would appeal to the collegium to take their decision on the best interest of the country, based on the cherished values and principles and ideologies. These are social justice, inclusiveness and values of composite Indian heritage which we hold so sacred,&quot; she told reporters after she was declared as the unanimous opposition candidate.&nbsp;</p>\r\n', '3161_Politics.jpg', '1498168800', '', '', 243, 1),
(8, 4, 'video', 'NDA\'s Presidential Candidate Ram Nath Kovind reaches Parliament to file nomination', '<p>Ram Nath Kovind reaches Parliament to file nomination for the upcoming Presidential Election</p>\r\n', '74407_PoliticsVideo.jpg', '1498168800', '3sPr9gA9t8A', 'https://www.youtube.com/watch?v=3sPr9gA9t8A', 690, 1),
(9, 8, 'image', 'Funding shortage claim as Aids conference starts', '<p>The 2016 United Nations World Aids&nbsp;conference begins in Durban, South Africa today&nbsp;with nearly 18,000 delegates from around the world expected to participate.&nbsp;</p>\r\n\r\n<p>A major demonstration is planned at the start of the conference to draw attention to shortages in global aids funding.&nbsp;</p>\r\n\r\n<p>Sixteen years ago, the last time this meeting was held in South Africa,&nbsp;the battleground was over access to life saving drugs,&nbsp;now it&#39;s the demand to keep funding in line with scientific progress.&nbsp;</p>\r\n\r\n<p>This conference&nbsp;is seen as deeply symbolic in a country where, despite dramatic progress, a thousand people continue to get infected every day.</p>\r\n\r\n<p>The last time delegates gathered for a major aids conference in South Africa&nbsp;the country&#39;s leadership was in denial about the links between HIV&nbsp;and Aids.&nbsp;</p>\r\n\r\n<p>Now 16 years later&nbsp;it has tried to turn things around and now has&nbsp;one of the largest HIV&nbsp;treatment programmes in the world. Fewer&nbsp;babies are now being born with the disease,&nbsp;but one in five South Africans are still&nbsp;living with HIV.</p>\r\n', '86560_World.jpg', '1498600800', '', '', 260, 1),
(10, 1, 'image', 'African Champions League: Mamelodi Sundowns beat Zamalek', '<p>South Africa&#39;s Mamelodi Sundowns beat Egypt&#39;s Zamalek 2-1 in Cairo on Sunday to close in on a place in the semi-finals of the African Champions League.</p>\r\n\r\n<p>The win means Sundowns are just a point from a place in the last four - with two games remaining.</p>\r\n\r\n<p>They only reached the group phase after the disqualification of DR Congo&#39;s Vita Club, for using an ineligible player.</p>\r\n\r\n<p>In Saturday&#39;s matches in the other group, Zesco United beat ASEC Mimosas and Al Ahly and Wydad Casablanca drew.</p>\r\n\r\n<p>Sundowns&#39; victory came thanks to goals from Tiyani Mabunda and Khama Billiat.</p>\r\n', '26266_Sports.jpg', '1497477600', '', '', 274, 1),
(11, 1, 'video', 'Worst Mankheding in Cricket History', '<p>The win means Sundowns are just a point from a place in the last four - with two games remaining.</p>\r\n\r\n<p>They only reached the group phase after the disqualification of DR Congo&#39;s Vita Club, for using an ineligible player.</p>\r\n\r\n<p>In Saturday&#39;s matches in the other group, Zesco United beat ASEC Mimosas and Al Ahly and Wydad Casablanca drew.</p>\r\n\r\n<p>Sundowns&#39; victory came thanks to goals from Tiyani Mabunda and Khama Billiat.</p>\r\n', '820_Sportsvideo.jpg', '1498168800', 'cYTxl7J69Wg', 'https://www.youtube.com/watch?v=cYTxl7J69Wg', 319, 1),
(14, 2, 'video', 'Golden Globes 2016: Hottest Looks and Fashion Trends', '<p>Yahoo Style editor-in-chief Joe Zee shares his favorite looks from the Golden Globes red carpet.&nbsp;According to government sources, there were several unconfirmed reports emerging on the status of the operation by Bangladesh security forces and therefore, India was ascertaining the facts.<br />\r\nIndia is closely monitoring the situation. Reports received so far said India High Commission officials are safe, sources said.<br />\r\nBangladesh&#39;s hostage crisis began when suspected ISIS terrorists stormed a restaurant in Dhaka&#39;s high-security Gulshan diplomatic area last night and held many people hostage, including foreigners.</p>\r\n', '4455_fashionvideo.jpg', '1498168800', '9rdsY97MUfc', 'https://www.youtube.com/watch?v=9rdsY97MUfc', 614, 1),
(15, 2, 'image', 'Emma Watson\\\'s Fashion Evolution', '<p>According to government sources, there were several unconfirmed reports emerging on the status of the operation by Bangladesh security forces and therefore, India was ascertaining the facts.<br />\r\nIndia is closely monitoring the situation. Reports received so far said India High Commission officials are safe, sources said.<br />\r\nBangladesh&#39;s hostage crisis began when suspected ISIS terrorists stormed a restaurant in Dhaka&#39;s high-security Gulshan diplomatic area last night and held many people hostage, including foreigners.</p>\r\n', '76449_Fashion.jpg', '1497996000', '', '', 460, 1),
(16, 3, 'image', 'Top Indian acts in International Got Talent', '<p>Top Indian acts in International Got Talent shows Britain Asia America etc<br />\r\nThe winners of the competition were announced on the weekend and are on display at the Parkes Observatory in New South Wales.</p>\r\n', '80535_entertainment.jpg', '1497996000', '', '', 348, 1),
(17, 3, 'video', 'Guinness World Record In Britain’s Got Talent 2017', '<p><br />\r\nThe US has said it cannot yet confirm the ISIS&#39;s claim owning responsibility for the hostage crisis in a restaurant in Dhaka&#39;s high security diplomatic area.<br />\r\n<br />\r\n&quot;We have seen ISIL (ISIS) claims of responsibility, but cannot yet confirm and are assessing the information available to us,&quot; State Department spokesman John Kirby said.<br />\r\n<br />\r\nAccording to US media reports, ISIS has claimed responsibility of the attack at Holey Artisan Bakery in Dhaka last night.</p>\r\n', '109_entertainmentvideo.png', '1497996000', 'QtFiQlBQQws', 'https://www.youtube.com/watch?v=QtFiQlBQQws', 671, 1),
(18, 7, 'video', 'Health and Fitness Q&A', '<p>But according to CNN, senior US officials believe that the attack has been probably carried out by Al Qaeda in Indian Sub-continent, which was declared as a terrorist organisation by the US only a day earlier.<br />\r\n<br />\r\n&quot;You can say we are aware of these reports but refer to Bangladeshi authorities,&quot; a senior administration official told PTI when asked about the news reports.<br />\r\n<br />\r\nBangladeshi commandos launched an operation to free at least 20 hostages, including several foreigners, from the restaurant in Dhaka&#39;s high-security Gulshan diplomatic area that was stormed by suspected ISIS terrorists, in which two policemen were killed and 30 others injured.</p>\r\n', '60357_Healthvideo.jpg', '1497391200', '3RhBqNzCMG8', 'https://www.youtube.com/watch?v=3RhBqNzCMG8', 776, 1),
(19, 7, 'image', 'What I Ate In A Day To LOSE WEIGHT: 20 KGS!', '<p>Hey guys &amp; welcome back! Please view in HD!<br />\r\n<br />\r\nToday&#39;s video is showing you guys what I ate today. The meals shown in this video are what I ate to lose my weight throughout my journey and also what I eat to maintain the weight loss. If you would like to see more of these videos showing you healthy meals, then please let me know :) I really hope you enjoy this.</p>\r\n', '52288_Health.jpg', '1498082400', '', '', 340, 1),
(20, 6, 'video', 'World Most Amazing Latest Technology Military Equipment', '<p>United States President Barrack Obama was also briefed by his top counter-terrorism official last night on the attack.<br />\r\n<br />\r\n&quot;Assistant to the President for Homeland Security and Counter terrorism Lisa Monaco has briefed the President on the ongoing situation in Dhaka, Bangladesh. The President asked to be kept informed as the situation develop,&quot; a senior White House official said.<br />\r\n<br />\r\nThe State Department said it is monitoring the situation and has offered assistance to Bangladesh and all Americans at the US Embassy has been accounted for.</p>\r\n', '95053_Technologyvideo.jpg', '1497996000', 'sUnn-SMXXOE', 'https://www.youtube.com/watch?v=sUnn-SMXXOE', 452, 1),
(21, 6, 'image', 'Mobile Phone Photography Technology Gadgets You Should Have', '<p>According to US media reports, ISIS has claimed responsibility of the attack at Holey Artisan Bakery in Dhaka last night.<br />\r\n<br />\r\nBut according to CNN, senior US officials believe that the attack has been probably carried out by Al Qaeda in Indian Sub-continent, which was declared as a terrorist organisation by the US only a day earlier.<br />\r\n<br />\r\n&quot;You can say we are aware of these reports but refer to Bangladeshi authorities,&quot; a senior administration official told PTI when asked about the news reports.<br />\r\n<br />\r\nBangladeshi commandos launched an operation to free at least 20 hostages, including several foreigners, from the restaurant in Dhaka&#39;s high-security Gulshan diplomatic area that was stormed by suspected ISIS terrorists, in which two policemen were killed and 30 others injured.<br />\r\n<br />\r\nUnited States President Barrack Obama was also briefed by his top counter-terrorism official last night on the attack.</p>\r\n', '90249_Technology.jpg', '1497225600', '', '', 516, 1),
(22, 8, 'video', 'Important World News Headlines Today', '<p>United States President Barrack Obama was also briefed by his top counter-terrorism official last night on the attack. world news today, top world news, important world news, news update, world news headlines, world headline news, breaking world news, end time news, end times update, latest endtime news.<br />\r\n<br />\r\nThe State Department said it is monitoring the situation and has offered assistance to Bangladesh and all Americans at the US Embassy has been accounted for.</p>\r\n', '19616_Worldvideo.jpg', '1497996000', 'SayZV8BEMw8', 'https://www.youtube.com/watch?v=SayZV8BEMw8', 2718, 1),
(23, 4, 'video', '7th pay commission: Central government employees for these 2 points', '<p><strong>7th pay commission latest news today:</strong>&nbsp;Even as the big hike hopes of Central government employees have not materialised yet, this volatile space of expectations has not cooled down in terms of latest news and more. In fact, the government employees are still considering that the two big events in the&nbsp;coming months will get them what they have been demanding for long now. First, comes the Republic Day 2019, when the government usually makes&nbsp;new announcements. The second big event, in fact, the biggest of all, would be the Lok Sabha elections 2019 when parties - both in power and opposition - would leave no stone unturned to lure voters. Central government employees significantly constitute a large and influential group of voters in the country.&nbsp;</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>It is expected that before going to the polls, PM Narendra Modi-led Union government may announce some good news for the Central government employees.&nbsp;</p>\r\n\r\n<p>Central government employees are 50 lakh strong and almost a similar number of pensioners are there. They have all pinned their hopes on the present regime at the Centre agreeing to their long-standing demands for a hike in salaries as currently, inflation is eating into their earnings even as their needs, as well as those of their families, are rising. They are demanding that Centre hike the fitment factor from the current levels to 3.68 times. The&nbsp;7th pay commission report had recommended a hike in salaries via a formula that had fixed the fitment factor at 2.57 times. The salaries at the minimum&nbsp;level had thereafter, risen to Rs 18,000, but the demand is to raise them to Rs 26,000.</p>\r\n\r\n<p>With so much going on, keep abreast of the 7th Pay Commission latest news here:&nbsp;</p>\r\n\r\n<p>* HRA of 23,000 staff in Puducherry has been hiked at a cost of Rs 6 cr every month.&nbsp;</p>\r\n\r\n<p>* 16 per cent of basic pay has been cleared for those in Puducherry. Staff in Mahe, Yanam and Karaikal would get 8 per cent.</p>\r\n\r\n<p>* Tripura government has cleared the implementation of National Payment System (NPS). This is in wake of their earlier demands for 7th pay commission to be implemented for them having been cleared.</p>\r\n', '15513_50330-rupee-reuters.jpg', '1535414400', 'JBPleD-CbKA', 'https://www.youtube.com/watch?v=JBPleD-CbKA', 2, 1),
(24, 1, 'image', 'Tendulkar was great, Kohli more damaging: David Lloyd', '<p><strong>LONDON</strong>: We know David Lloyd&nbsp;as a well-known commentator who helps to bring forth the lighter side of the narrative in addition to serious analysis of the game. It&rsquo;s another matter though that Bumble (his popular nickname) had played nine Test matches for England.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>But even while discussing broadcasting, it didn&rsquo;t take him too long to get into the VIrat Kohli-topic. <strong><em>&ldquo;Kohli is such a broadcaster&rsquo;s delight, he goes through every emotion on the field. I have met him outside cricket&nbsp;too, he is so polite, but on the field he is so animated and it&rsquo;s impossible to take the camera off him</em></strong>,&rdquo; Bumble said.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Explaining the <strong>Kohli magic</strong> on television, the former England player says that the way he leads the team on the field is also a broadcaster&rsquo;s delight. &ldquo;<em><strong>The way he constantly talks and inspires the bowlers is quite a treat. He obviously gets the respect because he is a fabulous player, but you can see that the emotion apart, he is very honest with his players as well. There&rsquo;s been a massive turnaround in this Indian team in the way quick bowlers have taken over completely and I feel Kohli is the perfect man to develop them. He looks a natural leader</strong></em>,&rdquo; the former Lancashire man, who has 19,269 runs in 407 first-class games, added.</p>\r\n', '88121_fb600_1495604810.jpg', '1535414400', '', '', 8, 1),
(25, 1, 'image', 'Asian Games 2018: India\\\'s medal tally after Day 9', '<p>India had another fruitful day in track and field events as athletes returned with gold and silver medals after shuttler Saina Nehwal&#39;s bronze beame the first individual badminton medal at the Asian Games for India in 36 years.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Neeraj Chopra was undoubtedly the star of the day for India, after he became the first Indian javelin thrower to win an Asian Games gold medal when he shattered his own national record by clearing a distance of 88.06m.</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Chopra&#39;s gold is India&#39;s only second medal in javelin throw in Asian Games history after Gurtej Singh won a bronze in 1982 in New Delhi</p>\r\n\r\n<p>&nbsp;</p>\r\n\r\n<p>Dharun Ayyasamy bagged silver in men&#39;s 400 metre hurdles at the 2018 Asian Games while Sudha Singh won silver in women&#39;s 3000 metre steeplechase on Monday in Jakarta. Neena Varakil also won silver in women&#39;s long jump with a jump of 6.51.</p>\r\n', '8275_Neeraj-Chopra_1.jpeg', '1535500800', '', '', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_news_gallery`
--

CREATE TABLE `tbl_news_gallery` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `news_gallery_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_news_gallery`
--

INSERT INTO `tbl_news_gallery` (`id`, `news_id`, `news_gallery_image`) VALUES
(12, 7, '93292_Politics2.jpg'),
(13, 7, '83398_politics1.jpg'),
(16, 9, '3970_World1.jpg'),
(17, 9, '50128_World2.jpg'),
(18, 10, '89782_Sports1.jpg'),
(19, 10, '5239_Sports2.jpg'),
(24, 15, '15991_Fashion1.jpg'),
(25, 15, '93215_fashion2.jpg'),
(26, 16, '56710_entertainment1.jpg'),
(27, 16, '20715_entertainment2.png'),
(30, 19, '40280_health1.jpg'),
(31, 19, '42272_health2.jpg'),
(32, 19, '51675_health3.jpg'),
(34, 21, '79571_Technology1.jpg'),
(35, 21, '95346_Technology2.jpg'),
(36, 24, '3019_kohli-sachin-13-1460545806.jpg');

-- --------------------------------------------------------
 
--
-- Table structure for table `tbl_settings`
--

CREATE TABLE `tbl_settings` (
  `id` int(11) NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `onesignal_app_id` varchar(500) NOT NULL,
  `onesignal_rest_key` varchar(500) NOT NULL,
  `app_name` varchar(255) NOT NULL,
  `app_logo` varchar(255) NOT NULL,
  `app_email` varchar(255) NOT NULL,
  `app_version` varchar(255) NOT NULL,
  `app_author` varchar(255) NOT NULL,
  `app_contact` varchar(255) NOT NULL,
  `app_website` varchar(255) NOT NULL,
  `app_description` text NOT NULL,
  `app_developed_by` varchar(255) NOT NULL,
  `app_privacy_policy` text NOT NULL,
  `api_latest_limit` int(3) NOT NULL,
  `api_cat_order_by` varchar(255) NOT NULL,
  `api_cat_post_order_by` varchar(255) NOT NULL,
  `publisher_id` varchar(500) NOT NULL,
  `interstital_ad` varchar(500) NOT NULL,
  `interstital_ad_id` varchar(500) NOT NULL,
  `interstital_ad_click` varchar(500) NOT NULL,
  `banner_ad` varchar(500) NOT NULL,
  `banner_ad_id` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `email_from`, `onesignal_app_id`, `onesignal_rest_key`, `app_name`, `app_logo`, `app_email`, `app_version`, `app_author`, `app_contact`, `app_website`, `app_description`, `app_developed_by`, `app_privacy_policy`, `api_latest_limit`, `api_cat_order_by`, `api_cat_post_order_by`, `publisher_id`, `interstital_ad`, `interstital_ad_id`, `interstital_ad_click`, `banner_ad`, `banner_ad_id`) VALUES
(1, '', '9671dfb6-7953-4faf-8328-364472500576', 'MTc1M2YzZDYtNGY2Zi00ZTdjLTkxNDEtMDk1OGMyYmVhYjE0', 'All in One News App', 'app_icon.png', 'info@viaviweb.com', '2.1.5', 'Viavi Webtech', '+91 9227777522', 'www.viaviweb.com', '<p>Watch your favorite TV channels Live in your mobile phone with this Android application on your Android device. that support almost all format.The application is specially optimized to be extremely easy to configure and detailed documentation is provided.</p>\n', 'Viavi Webtech', '<p><strong>We are committed to protecting your privacy</strong></p>\r\n\r\n<p>We collect the minimum amount of information about you that is commensurate with providing you with a satisfactory service. This policy indicates the type of processes that may result in data being collected about you. Your use of this website gives us the right to collect that information.&nbsp;</p>\r\n\r\n<p><strong>Information Collected</strong></p>\r\n\r\n<p>We may collect any or all of the information that you give us depending on the type of transaction you enter into, including your name, address, telephone number, and email address, together with data about your use of the website. Other information that may be needed from time to time to process a request may also be collected as indicated on the website.</p>\r\n\r\n<p><strong>Information Use</strong></p>\r\n\r\n<p>We use the information collected primarily to process the task for which you visited the website. Data collected in the UK is held in accordance with the Data Protection Act. All reasonable precautions are taken to prevent unauthorised access to this information. This safeguard may require you to provide additional forms of identity should you wish to obtain information about your account details.</p>\r\n\r\n<p><strong>Cookies</strong></p>\r\n\r\n<p>Your Internet browser has the in-built facility for storing small files - &quot;cookies&quot; - that hold information which allows a website to recognise your account. Our website takes advantage of this facility to enhance your experience. You have the ability to prevent your computer from accepting cookies but, if you do, certain functionality on the website may be impaired.</p>\r\n\r\n<p><strong>Disclosing Information</strong></p>\r\n\r\n<p>We do not disclose any personal information obtained about you from this website to third parties unless you permit us to do so by ticking the relevant boxes in registration or competition forms. We may also use the information to keep in contact with you and inform you of developments associated with us. You will be given the opportunity to remove yourself from any mailing list or similar device. If at any time in the future we should wish to disclose information collected on this website to any third party, it would only be with your knowledge and consent.&nbsp;</p>\r\n\r\n<p>We may from time to time provide information of a general nature to third parties - for example, the number of individuals visiting our website or completing a registration form, but we will not use any information that could identify those individuals.&nbsp;</p>\r\n\r\n<p>In addition Dummy may work with third parties for the purpose of delivering targeted behavioural advertising to the Dummy website. Through the use of cookies, anonymous information about your use of our websites and other websites will be used to provide more relevant adverts about goods and services of interest to you. For more information on online behavioural advertising and about how to turn this feature off, please visit youronlinechoices.com/opt-out.</p>\r\n\r\n<p><strong>Changes to this Policy</strong></p>\r\n\r\n<p>Any changes to our Privacy Policy will be placed here and will supersede this version of our policy. We will take reasonable steps to draw your attention to any changes in our policy. However, to be on the safe side, we suggest that you read this document each time you use the website to ensure that it still meets with your approval.</p>\r\n\r\n<p><strong>Contacting Us</strong></p>\r\n\r\n<p>If you have any questions about our Privacy Policy, or if you want to know what information we have collected about you, please email us at hd@dummy.com. You can also correct any factual errors in that information or require us to remove your details form any list under our control.</p>\r\n', 10, 'category_name', 'news_heading', 'pub-8356404931736973', 'true', 'ca-app-pub-8356404931736973/8732534868', '5', 'true', 'ca-app-pub-8356404931736973/9694015321');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `fb_id` varchar(255) NOT NULL,
  `gplus_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `confirm_code` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 
--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `tbl_channel`
--
ALTER TABLE `tbl_channel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comments`
--
ALTER TABLE `tbl_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_news`
--
ALTER TABLE `tbl_news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_news_gallery`
--
ALTER TABLE `tbl_news_gallery`
  ADD PRIMARY KEY (`id`);
 

--
-- Indexes for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_channel`
--
ALTER TABLE `tbl_channel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_comments`
--
ALTER TABLE `tbl_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `tbl_news`
--
ALTER TABLE `tbl_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_news_gallery`
--
ALTER TABLE `tbl_news_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
 
--
-- AUTO_INCREMENT for table `tbl_settings`
--
ALTER TABLE `tbl_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
