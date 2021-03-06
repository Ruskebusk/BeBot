DROP TABLE IF EXISTS `pocketbosses`;
CREATE TABLE `pocketbosses` (
  `ID` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `is_pocket` TINYINT(1) DEFAULT '1',
  `name` VARCHAR(50) COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Broken!',
  `level` INTEGER(11) DEFAULT NULL,
  `Playfield` VARCHAR(30) COLLATE latin1_swedish_ci DEFAULT NULL,
  `Place` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL,
  `pattern_mobs` VARCHAR(50) COLLATE latin1_swedish_ci DEFAULT NULL,

  PRIMARY KEY (`ID`)
)ENGINE=MyISAM COMMENT='' CHECKSUM=0 DELAY_KEY_WRITE=0 PACK_KEYS=0 MIN_ROWS=0 MAX_ROWS=0 ROW_FORMAT=DYNAMIC CHARACTER SET 'latin1'
COLLATE 'latin1_swedish_ci' INSERT_METHOD=NO;
/*!40000 ALTER TABLE `pocketbosses` DISABLE KEYS */;
LOCK TABLES `pocketbosses` WRITE;
INSERT INTO `pocketbosses` (`ID`, `is_pocket`, `name`, `level`, `playfield`, `place`, `pattern_mobs`) VALUES 
  (1, 0, 'Mesut-Rat', 25, 'Nascense', 'Two Mountains (north of)', 'Named Croaker Boss'),
  (2, 1, 'Black Fang', 80, 'Central Elysium', 'Shattered Heartlands (SE from Nero)', 'Named Predators (4): Limping, Howling, Pained, Wou'),
  (3, 1, 'Silver Fang', 80, 'Central Elysium', 'SE from Nero', '4 Predator Cat Boss'),
  (4, 1, 'Circumbendibus', 80, 'Elysium', 'Southmire (SW Swamps)', 'Brutish Dryad'),
  (5, 1, 'Gracious Soul Dredge', 115, 'Scheol Lower', 'Necropolis', 'Soul Dredges'),
  (6, 1, 'Spiritless Soul Dredge', 115, 'Scheol Lower', 'Necropolis', 'Spirit Soul Dredge'),
  (7, 1, 'Adobe Suzerain', 125, 'Scheol Upper', 'Highlands NW Incarnator N', 'Eremites'),
  (8, 1, 'Devourer of Scheol', 205, 'Scheol Lower', 'Eastern Micro Islands', 'Greek Named Brink Mobs'),
  (9, 1, 'Shake', 192, 'Adonis Abyss NW', 'Icicle Keep (NW White Cave)', 'Named Slithers'),
  (10, 1, 'Defiler of Scheol', 220, 'Scheol Lower', 'Halls of Scheol - Eastern', 'Greek Named Brink Mobs'),
  (11, 0, 'Ahwere', 30, 'Nascense Wilds', 'Back of the Crippler Cave [662,1213]', 'Croakers'),
  (12, 1, 'Gunk', 125, 'Scheol Upper', 'Temple Bog (swamp under bridge)', 'Named Rafters (4)'),
  (13, 1, 'Old Salty', 165, 'Adonis Abyss', 'West / North', 'Named Dryads (6)'),
  (14, 1, 'Toss', 195, 'Adonis West Brink', 'Hollow Maze', 'Named Golems'),
  (15, 1, 'Ahpta', 220, 'Inferno Frontier', 'Each side of the ramp to Razor''s Lair', 'Named Spinetooth: Atakirh & Salahpt'),
  (16, 1, 'Shullat', 255, 'Inferno Frontier', 'Clan Sanctuary Exit: Razor''s Lair', 'Tinos, Astypalia, Phatmos'),
  (17, 0, 'Demonic Subjugator', 20, 'Nascense', 'West from the Breach in the Wall', 'Scourge'),
  (18, 1, 'Careening Blight', 45, 'Nascense', 'North of Brawl', 'Named Weaver Boss'),
  (19, 1, 'Circumbendibum', 80, 'Elysium', 'Southmire', 'Named Dryads'),
  (20, 1, 'The Dune Suzerain', 90, 'Elysium Northwest', 'The Brink - Tinker Tower - Shell Beach', 'Coiling Eremite and Others (310x1260)'),
  (21, 1, 'Iunmin', 110, 'Scheol Lower', 'Eastern Micro Islands', 'Croaker of Night'),
  (22, 1, 'Anya', 185, 'Penumbra Valley', 'SW from Spirit Palace - N from Ruins', 'Savvy Spinetooth Bosses: Maywakaham, Waqamawi'),
  (23, 1, 'Quondam', 215, 'Penumbra Valley', 'Frozen Plateu Island', 'Every mobs on the Isle'),
  (24, 1, 'Agent of Decay', 255, 'Inferno Frontier', 'Near 225 Incarnator (Eremites)', 'Eremites East from Hebiel & Ahomac'),
  (25, 0, 'Rainbow Feathers', 15, 'Nascense', 'Location: 1098x1267', 'Tempterus (Redeemed)'),
  (26, 0, 'Takheperu', 25, 'Nascense', 'Two Mountains (north of)', 'Named Croaker Boss (162 x 734)'),
  (27, 1, 'Ignis Fatuus', 125, 'Scheol Lower', 'Rock Bottom', 'Shadows and Demons'),
  (28, 1, 'Marem', 125, 'Scheol Lower', 'Eastern Side', 'Birds/Imps'),
  (29, 1, 'Zoetic Oak', 180, 'Penumbra Forest', 'SW Glacier Hill - SW Statue', 'Stubby Stalker and Named'),
  (30, 1, 'Alatyr', 188, 'Penumbra Forest', 'SW Glacier Hill - SW Statue', 'Stubby Stalker and Named'),
  (31, 1, 'Imk', 185, 'Penumbra Forest', 'Cave North from West Hollow Entrance', 'Named Malah and Spinetooth'),
  (32, 1, 'Juma', 182, 'Penumbra Hollows', 'Middle/South and Sup Level', 'Named Hiisi Warriors (8)'),
  (33, 1, 'Ushqa', 220, 'Inferno', 'Oasis, south from Inferno Frontier', 'Maychwaham, Ushamaham'),
  (34, 1, 'Haqa', 220, 'Inferno', 'N from Oasis, S from Frontier', 'Akshk''i & Tuq''usk'),
  (35, 0, 'Massive Waggler', 0, 'Nascense', 'Loc: 453, 331', '???'),
  (36, 1, 'Ashmara Ravin', 125, 'Scheol Upper', 'The Plundge', 'Arcorash and Named'),
  (37, 1, 'Lethargic Spirit', 125, 'Scheol Lower', 'Necropolis (Ergo)', 'Spirits/Spirit Hunters'),
  (38, 1, 'Srahir', 180, 'Penumbra Hollows', 'South Shadow Camps', 'Shadows and Hiathlins'),
  (39, 1, 'Waqa', 220, 'Inferno Yuttos Marshes', 'South of Xark''s Lair - North from Yuttos', 'Natsmaahpt, Sanatsimk'),
  (40, 0, 'Hateful Weaver', 40, 'Nascense Wilds', 'North from Brawl - (218 x 1261)', 'Spider Boss'),
  (41, 1, 'Scratch', 80, 'Central Elysium (W)', 'West of Yuttos, NE of Boneyard', 'Named Rafters'),
  (42, 1, 'Screech', 80, 'Central Elysium (W)', 'NE from Boneyard, W from Yuttos', 'Named Rafters'),
  (43, 1, 'Auger', 125, 'Scheol Lower', 'Tree of Memories', 'Spiders around'),
  (44, 1, 'Awl', 125, 'Scheol Lower', 'Tree of Memories (W)', 'Green named Spiders'),
  (45, 1, 'Sampsa', 185, 'Penumbra Valley', 'North from Frozen Plateu', 'Slender Rafter (Dyna)'),
  (46, 1, 'The Maggot Lord', 205, 'Penumbra Forest', 'N of Blue Mist - South of The Pipe', 'Named Eremites'),
  (47, 1, 'Morrow', 215, 'Penumbra Valley', 'Frozen Plateu Island', 'Every mobs on the Isle'),
  (48, 0, 'Nesbaneb', 25, 'Nascense', 'Two Mountains (north of)', 'Named Croaker Boss (140 x 664)'),
  (49, 1, 'Thunderous Chimera', 125, 'Scheol Upper', 'Marble Orchards, East Passage', 'Chimera'),
  (50, 1, 'Smee', 165, 'Adonis Abyss', 'West / North', 'Named Dryads (6)'),
  (51, 0, 'Vile Weaver', 40, 'Nascense Wilds', 'North exit of Brawl (210x1160)', 'Spider boss'),
  (52, 1, 'Brutal Soul Dredge', 85, 'East Elysium', 'Linger Echo', 'Spirit Soul Dredge'),
  (53, 1, 'Misery', 125, 'Scheol Lower', 'Necropolis NE', 'Noxious Minx'),
  (54, 1, 'Blight', 125, 'Scheol Lower', 'Necropolis (Ergo)', 'Mephitic Mynx'),
  (55, 1, 'Kaleva', 182, 'Penumbra Hollows', 'In Corridors and on the South 2nd Floor', 'Named Hiisi Warrior Boss (8)'),
  (56, 0, 'Predator Rager', 5, 'Nascense', 'Loc: 364, 285', 'Panther'),
  (57, 0, 'Hardened Mortiig', 8, 'Nascense', 'Loc: 523, 215', 'Mortiig'),
  (58, 1, 'Asase Ya', 255, 'Inferno Burning Marshes', 'SW limits of Inferno by Sorrow South', 'Asase''s Drudge, Nyame''s Abettor, Asase''s Child'),
  (59, 1, 'The Talus Suzerain', 90, 'Elysium Northwest', 'The Brink - Tinker Tower - Shell Beach', 'Coiling Eremite and Named (310x1260)'),
  (60, 1, 'Ungulera', 90, 'East Elysium', 'Fission Fissure', 'Named Hiathlin'),
  (61, 1, 'Borer', 125, 'Scheol Upper', 'The Eastern Passageway/Crystal River', 'Weavers/Spiders'),
  (62, 1, 'Beatific Spirit', 125, 'Scheol Lower', 'Necropolis', 'Spirit Hunters'),
  (63, 1, 'Redeemed Gilthar', 175, 'Adonis', 'Redeemed Temple & Outside', 'Redeemed Faction Boss & Named Redeemed'),
  (64, 1, 'The Penumbral Spirit Hunter', 165, 'Penumbra Valley', 'Spirit Ruins', 'Vamoosing/Skedaddling Spirit'),
  (65, 1, 'Aray', 210, 'Penumbra Forest', 'West Brink / South from Icarnator', 'Alonissos / Levkada'),
  (66, 1, 'Tcheser', 48, 'Nascense', 'Around Two Mountains', 'Croaker Boss all around'),
  (67, 1, 'Bellowing Chimera', 125, 'Scheol Upper', 'North from East Incarnator', 'Named Chimeras'),
  (68, 1, 'Quake', 195, 'Adonis SE', 'In the Sand', 'Whirling Rocks and Named Golems'),
  (69, 1, 'Bagaspati', 185, 'Penumbra Valley', 'NW from Incarnator', 'Named Rafter/Cat/Vadleany'),
  (70, 1, 'Khemhet', 110, 'Scheol Upper', 'NorthEast from Temple Bog', '6 Named: Thapt, Kheferu, Nabur...'),
  (71, 1, 'Contorted Soul Dredge', 125, 'Scheol Lower', 'Necropolis', 'Soul Dredges'),
  (72, 1, 'Bhinaji Navi', 125, 'Scheol Lower', 'Through Earth & Stone Pyramid', 'Chimeran Canine, Ercorash and Named(4)'),
  (73, 1, 'Marly Suzerain', 125, 'Scheol Upper', 'West Incarnator & NW', 'Eremites'),
  (74, 1, 'Taille Frees', 220, 'Inferno Frontier', 'Path to the Redeemed Temple - Aniitap Spot', 'Aniitap''s Shadow/Aniitap(250)/Wicked Shadow'),
  (75, 1, 'Sasabonsam', 220, 'Inferno Frontier', 'Dark Hills - SE from 255 incarnator', 'Anansi''s Disciple'),
  (76, 1, 'Shake (Golem)', 195, 'Adonis West Brink', 'Hollow Maze', 'Named Golems'),
  (77, 1, 'Careening Death', 45, 'Nascense', 'North of Brawl', 'Named Weaver Boss'),
  (78, 1, 'The Elysian Soul Dredge', 90, 'East Elysium', 'Linger Echo', 'Spirit Soul Dredge'),
  (79, 1, 'Brutal Rafter', 175, 'Penumbra Valley', 'NW from Incarnator', 'Named Rafter/Cat/Vadleany'),
  (80, 1, 'Breaker Teuvo', 175, 'Penumbra Forest', 'North from West Incarnator', 'Named Hiisi Steed: Shadowmane'),
  (81, 0, 'Starry Feathers', 15, 'Nascense', 'Location: 682x1274', 'Tempterus (Unredeemed)'),
  (82, 1, 'Argil Suzerain', 125, 'Scheol Upper', 'West Incarnator', 'Eremites'),
  (83, 0, 'Maynatsk', 40, 'Nascense Swamps', 'loc: 1768, 1134', 'Named Spinetooth'),
  (84, 1, 'Canceroid Cupid', 40, 'Nascence Wilds', 'Steppe of Dispair', 'Named Cripplers'),
  (85, 1, 'The Worm King', 202, 'Penumbra Forest', 'Muktuk Area SW from NW Red Temple', 'Death Wing & Named Slithers'),
  (86, 1, 'Wacky Suzerain', 125, 'Scheol Upper', 'NW from West Incarnator', 'Named Eremites: Coiling, Sparkling, Bending...'),
  (87, 1, 'The Peristaltic Aversion', 172, 'Adonis Abyss', 'Worlmhole', 'Eremites and Named'),
  (88, 1, 'The Peristaltic Abomination', 172, 'Adonis Abyss', 'Worlmhole', 'Eremites and Named'),
  (89, 1, 'Churn', 195, 'Adonis SE', 'In the Sand', 'Whirling Rocks and Named Golems'),
  (90, 1, 'Lya', 220, 'Inferno Frontier (near Ergo)', 'WSW from QL 255 Incarnator', 'Maychwyawi, Afreet Ellis, Steele Flair'),
  (91, 1, 'Relief Teals', 220, 'Inferno', 'SE Xark''s Lair Inferno', 'Pyiininnik & Pyiininnik''s Shadow'),
  (92, 1, 'Sabretooth Slicer', 35, 'Nascense Swamps', 'Wetlands', 'Sabretooths'),
  (93, 1, 'Hemut', 125, 'Scheol Upper', 'Croaker Pass & Facet Canyon (NE)', '6 Named Croakers: Thapt, Kheferu, Nabur...'),
  (94, 1, 'The Dryad Shuffle', 185, 'Penumbra Forest', 'Blue Mist - from S to N', 'Named Dryads & Hollows'),
  (95, 1, 'Shiver', 192, 'Adonis Abyss NW', 'Icicle Keep (NW White Cave)', 'Named Slithers'),
  (96, 0, 'Dryad Watcher', 30, 'Nascense', 'Loc: 381, 460', 'Dryad'),
  (97, 0, 'Remur-Nefer', 25, 'Nascense', 'Two Mountains (north of)', 'Named Croaker Boss (255 x 649)'),
  (98, 0, 'Shadowleet of Powah', 3, 'Nascense', 'Loc: 317, 422', 'Shadowleet'),
  (99, 0, 'Heru-Maat', 30, 'Nascense Wilds', 'Back of the Crippler Cave [660,1200]', 'Croaker boss'),
  (100, 1, 'Ooze', 125, 'Scheol Upper', 'The Temple Bog (Swamp under Bridge)', 'Named Girders: Dirty, Sloughy, ...'),
  (101, 1, 'The Scheolian Soul Dredge', 125, 'Scheol Lower', 'Necropolis (Ergo)', 'Spirits/Spirit Hunters'),
  (102, 1, 'Kaoline Suzerain', 125, 'Scheol Upper', 'North from West Incarnator', 'Eremites'),
  (103, 1, 'Skulky', 165, 'Adonis NW', 'The Jaws', 'Skulking Dryad'),
  (104, 1, 'Slinky', 165, 'Adonis NW', 'The Jaws', 'Named Dryads (5)'),
  (105, 1, 'Lurky', 165, 'Adonis NW', 'The Jaws', 'Named Dryads (5)'),
  (106, 1, 'The Dryad Demigod', 220, 'Inferno Frontier', 'East from QL 255 Incarnator', 'Apprentice Beasthandler/Scrupulous Hiathlin/Puncti'),
  (107, 1, 'Pazuzu', 220, 'Inferno Frontier', 'QL 255 Incarnator', 'Crete, Syros, Limnos'),
  (108, 1, 'Tough', 125, 'Scheol Lower', 'The Approach (South of Necropolis)', 'Rigid Solid Robust Girders'),
  (109, 0, 'Hesoas', 15, 'Nascense', 'Frontier Outskirts', 'Named Hiathlin: Hesoas'),
  (110, 1, 'Upenpet', 48, 'Nascense', 'Around Two Mountains', 'Croaker Boss all around'),
  (111, 1, 'Loessial Suzerain', 125, 'Scheol Upper', 'West Incarnator', 'Eremites'),
  (112, 0, 'Leader of the Pack', 5, 'Nascense', 'Loc: 366, 247', 'Named Chimera'),
  (113, 1, 'K', 220, 'Inferno Frontier - Eastern', '225 Incarnator - East from Xark''s Lair', 'Named Spinetooth Bosses'),
  (114, 1, 'The Archbile Queen', 120, 'Elysium', 'Just South from Ergo', 'Croaker & Minx'),
  (115, 1, 'White', 120, 'Elysium South', 'White Pass (The Scoop)', 'Chill Spider & 2 Named Bosses'),
  (116, 1, 'The Numb One', 120, 'Elysium', 'Elysium (SW ?)', 'Mortiig bosses'),
  (117, 1, 'The Enrapt One', 120, 'Elysium', 'SW Elysium', 'Mortiig bosses'),
  (118, 1, 'Moochy', 165, 'Adonis North', 'The Jaws', 'Moochy&Named Dryads'),
  (119, 1, 'Aesma Daeva', 220, 'Inferno Frontier', 'Incarnator QL 255', 'Ithaki & Spetses'),
  (120, 1, 'Arch Bigot Biap', 220, 'Inferno Dark Hill', 'North from Incarnator 255', 'Obsolete Soul Dredge'),
  (121, 1, 'Fester Leila', 220, 'Inferno', 'East from QL 225 Incarnator', 'Distracted Snake Tamer, Beast, Estella Fire Dryad'),
  (122, 0, 'Malah-Alinos', 40, 'Nascense Swamps', 'The Core, loc: 1620,1240', 'Named Malah'),
  (123, 0, 'Crippler Fiend', 6, 'Nascense', 'Loc: 512, 303', 'Crippler'),
  (124, 1, 'Viscious Visitant', 165, 'Adonis SW', 'The Suburbs', 'Spirits'),
  (125, 1, 'The Adonian Soul Dredge', 165, 'Adonis Ark 1', 'Dead Ends Ancient Device Ark 1', 'Soul Dredges'),
  (126, 1, 'The Watchdog', 165, 'Adonis North', 'Sacellum & Sea of Adonis', 'Spirits/Souls'),
  (127, 1, 'The Dredge Driver', 165, 'Adonis City', 'The Suburbs/The Dig', 'Spirit Soul Dredge'),
  (128, 1, 'The Indomitable Chimera', 220, 'Inferno', 'Burning Marshes - Misty Marshes Statue', 'Chimera Mornitor/Trainer/Driver'),
  (129, 1, 'Ats (Ats`usk)', 220, 'Inferno', 'Oasis - Inferno Frontier', 'Isham, Spinetooths, Marsh Lizards'),
  (130, 1, 'Rallies Fete', 235, 'Inferno', 'North from Sorrow', 'Tiunissik Spot'),
  (131, 1, 'Satkamear', 125, 'Scheol Lower', 'Halls of Scheol - Bottom Level', 'Asatix Caal Coal Benacen Snaahemiel'),
  (132, 1, 'Loltonunon', 125, 'Scheol Lower', 'Hall of Scheol, The Approach', '5 Named Hiathlin'),
  (133, 1, 'The Adonis Spirit Master', 165, 'Adonis North', 'Sacellum & Sea of Adonis & Ark1', 'Spirits/Souls/Dredges'),
  (134, 1, 'The Proprietrix', 165, 'Adonis City', 'The Suburbs/The Dig', 'Soul Dredges'),
  (135, 1, 'The Abyssal Widow', 165, 'Adonis Abyss', 'Abyss SW', 'TinyTripTrapTrup Named Spiders'),
  (136, 1, 'Captured Spirit', 220, 'Inferno', 'Burning Marshes NW', 'Spirit Hunters/Ashen Viper'),
  (137, 1, 'Odqan', 220, 'Inferno', 'SW Yuttos Marshes', 'Named Spirits: Enkindled 208'),
  (138, 1, 'The Infernal Soul Dredge', 220, 'Inferno', '???', 'Named Spirits'),
  (139, 1, 'Ho', 220, 'Inferno', 'N from Oasis, S from Frontier', 'Ushamaham'),
  (140, 0, 'The Devastator', 8, 'Nascense', 'Near the Breach in the Wall', 'Demon or Panther'),
  (141, 1, 'Xark the Battletoad', 255, 'Penumbra Forest', 'South from Purity', 'Death Wing, Slithers, Maar...'),
  (142, 1, 'The Achbile Guardian', 120, 'Elysium', 'Just South from Ergo', 'Croaker & Minx'),
  (143, 1, 'The Abysmal Lord', 160, 'Adonis Abyss', 'The Depression or 1100x1500', 'TripTrapTrub Named Spiders'),
  (144, 1, 'The Brobdingnagian Mother', 185, 'Penumbra Forest', 'East of Blue Mist', 'Resolute Dryad'),
  (145, 1, 'The Stupendous Breeder', 185, 'Penumbra Forest', 'East Frozen Lakes from S to N', 'Named Dryads & Hollows'),
  (146, 1, 'Eidolean Soul Dredge', 180, 'Penumbra Forest', 'Corpse Sucker Cave NW', 'Exiguous&Maeger Spirit'),
  (147, 1, 'Glitter', 188, 'Penumbra Forest', 'Blue Mist - SW Lake in Forest', 'Chimeras: Sleet/Floe, Rafters: Snappy/Hefty/River/'),
  (148, 1, 'Exsequiae', 220, 'Inferno', 'North from Sorrow', 'Bigot Helozabasael, Torrid Spirit'),
  (149, 0, 'The Night Heart', 250, 'Pandemonium', 'Pandemonium Antenora (2nd Island)', 'The Night Heart'),
  (150, 0, 'Swiftwind', 10, 'Nascense', 'loc 780, 1268', 'Named Silvertail'),
  (151, 1, 'Hadur', 210, 'Penumbra Valley', 'SE from East Incarnator', 'Named Brink Mobs: Follegandros...'),
  (152, 1, 'Flinty', 125, 'Scheol Lower', 'The Approach (South of Necropolis)', 'Rigid Solid Robust Girders'),
  (153, 1, 'Sashu', 220, 'Inferno Yuttos Marshes', 'South of Xarks Lair - North from Yuttos', 'Sanatsimk, Ushap''ing, Waywaqa'),
  (154, 1, 'Anansis Adherent', 255, 'Inferno', 'East From 255', 'Scary Spider'),
  (155, 1, 'Anansi', 255, 'Inferno', 'Sorrow', 'Anansi''s Lhand,Anansi''s Rhand,Nyame''s Drudge'),
  (156, 1, 'Bia', 255, 'Inferno', 'WSW from QL 255 Incarnator', 'Bia''s Favorite, Anansi''s Favorite');
UNLOCK TABLES;
/*!40000 ALTER TABLE `pocketbosses` ENABLE KEYS */;