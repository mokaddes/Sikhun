/**
 * Verified Bangladesh Interactive Map Explorer Dataset (Bilingual EN / BN).
 * Sources:
 * - Official Government Portals: https://bangladesh.gov.bd/ & https://bangladesh.gov.bd/views/district-list
 * - Population & Area: Bangladesh Bureau of Statistics (BBS) Population & Housing Census 2022 (via Wikipedia)
 * - Public Universities: Wikipedia (List of universities in Bangladesh) & UGC Bangladesh
 * - Major Rivers: Wikipedia (List of rivers of Bangladesh) & BWDB
 * - Map Boundaries: geoBoundaries BGD ADM1 (CC0 1.0) & ADM2 (CC BY 3.0 IGO)
 */

export const BD_MAP_SOURCES = {
  "bn": "তথ্যসূত্র: বাংলাদেশ জাতীয় তথ্য বাতায়ন (bangladesh.gov.bd), বিবিএস জনশুমারি ২০২২ ও উইকিপিডিয়া। মানচিত্র: geoBoundaries (CC BY 3.0)।",
  "en": "Sources: Bangladesh National Portal (bangladesh.gov.bd), BBS Census 2022 & Wikipedia. Boundaries: geoBoundaries (CC BY 3.0)."
};

export const BD_DIVISIONS = {
  "dhaka": {
    "geoKey": "dhaka",
    "nameBn": "ঢাকা বিভাগ",
    "nameEn": "Dhaka Division",
    "iso": "BD-C",
    "hqBn": "ঢাকা",
    "hqEn": "Dhaka",
    "established": "1829",
    "areaKm2": 20508,
    "population": 44215783,
    "density": 2156,
    "districtCount": 13,
    "upazilaCount": 89,
    "parliamentSeats": 70,
    "districts": [
      "dhaka",
      "faridpur",
      "tangail",
      "gazipur",
      "narayanganj",
      "kishoreganj",
      "narsingdi",
      "munshiganj",
      "manikganj",
      "gopalganj",
      "shariatpur",
      "madaripur",
      "rajbari"
    ],
    "govUrl": "https://dhakadiv.gov.bd",
    "famousBn": "জাতীয় রাজধানী, পদ্মা সেতু, সোনারগাঁও, উয়ারী-বটেশ্বর, মধুপুর গড়, হাওর ও পাট-পোশাক শিল্প",
    "famousEn": "National Capital, Padma Bridge, Sonargaon, Wari-Bateshwar, Madhupur Tract, Industrial Hub",
    "historyBn": "১৮২৯ সালে ব্রিটিশ আমলে গঠিত ঢাকা বিভাগ বাংলাদেশের প্রশাসনিক, অর্থনৈতিক ও শিক্ষাগত প্রাণকেন্দ্র। ২০১৫ সালে ময়মনসিংহ পৃথক হওয়ার পর বর্তমানে ১৩টি জেলা নিয়ে গঠিত।",
    "historyEn": "Established in 1829 during British rule; administrative, economic, and educational core of Bangladesh. Comprises 13 districts since Mymensingh was carved out in 2015.",
    "examPointsBn": [
      "বাংলাদেশের সর্বাধিক জনবহুল বিভাগ (BBS ২০২২ অনুযায়ী ৪.৪ কোটির বেশি) এবং সর্বাধিক জেলা (১৩টি)",
      "দেশের ক্ষুদ্রতম জেলা নারায়ণগঞ্জ (৬৮৪ বর্গ কি.মি.) এই বিভাগে অবস্থিত",
      "পদ্মা বহুমুখী সেতু (মাওয়া–জাজিরা) ঢাকা বিভাগের মুন্সীগঞ্জ ও শরীয়তপুরকে যুক্ত করেছে",
      "প্রাচীন নগরী উয়ারী-বটেশ্বর (নরসিংদী) ও সোনারগাঁও (নারায়ণগঞ্জ) এই বিভাগে",
      "ঢাকাই জামদানি, টাঙ্গাইল শাড়ি ও পোড়াবাড়ির চমচম এই বিভাগের GI পণ্য"
    ],
    "examPointsEn": [
      "Most populous division in Bangladesh (44M+ in BBS 2022) with the most districts (13)",
      "Contains Bangladesh’s smallest district, Narayanganj (684 km²)",
      "Padma Multipurpose Bridge connects Munshiganj (Mawa) and Shariatpur (Jajira)",
      "Hosts ancient Wari-Bateshwar (Narsingdi) & Sonargaon (Narayanganj)",
      "Home to GI products: Dhakai Jamdani, Tangail Saree & Porabari Chomchom"
    ]
  },
  "chattogram": {
    "geoKey": "chattogram",
    "nameBn": "চট্টগ্রাম বিভাগ",
    "nameEn": "Chattogram Division",
    "iso": "BD-B",
    "hqBn": "চট্টগ্রাম",
    "hqEn": "Chattogram",
    "established": "1829",
    "areaKm2": 33907,
    "population": 33202326,
    "density": 979,
    "districtCount": 11,
    "upazilaCount": 104,
    "parliamentSeats": 58,
    "districts": [
      "chattogram",
      "cumilla",
      "noakhali",
      "rangamati",
      "bandarban",
      "khagrachhari",
      "brahmanbaria",
      "coxs-bazar",
      "chandpur",
      "lakshmipur",
      "feni"
    ],
    "govUrl": "https://chittagongdiv.gov.bd",
    "famousBn": "চট্টগ্রাম বন্দর, কক্সবাজার সমুদ্রসৈকত, সেন্ট মার্টিন, কাপ্তাই হ্রদ, সাজেক, ময়নামতি, কর্ণফুলী টানেল",
    "famousEn": "Chattogram Port, Cox’s Bazar Beach, St. Martin’s Island, Kaptai Lake, Sajek, Mainamati, Karnaphuli Tunnel",
    "historyBn": "১৮২৯ সালে গঠিত চট্টগ্রাম বিভাগ আয়তনে বাংলাদেশের বৃহত্তম বিভাগ। পার্বত্য ৩ জেলা, সমুদ্র উপকূল, সমুদ্রবন্দর ও প্রাকৃতিক গ্যাসক্ষেত্র নিয়ে এর অর্থনৈতিক গুরুত্ব অপরিসীম।",
    "historyEn": "Established in 1829; largest division of Bangladesh by area. Encompasses the Chittagong Hill Tracts, coastal belt, primary seaport, and major gas fields.",
    "examPointsBn": [
      "আয়তনে বাংলাদেশের বৃহত্তম বিভাগ (৩৩,৯০৯ বর্গ কি.মি.) এবং ১১টি জেলা নিয়ে গঠিত",
      "দেশের বৃহত্তম জেলা রাঙামাটি ও সর্বনিম্ন জনবহুল জেলা বান্দরবান এই বিভাগে",
      "বিশ্বের দীর্ঘতম সমুদ্রসৈকত কক্সবাজার ও একমাত্র প্রবাল দ্বীপ সেন্ট মার্টিন অবস্থিত",
      "দেশের একমাত্র জলবিদ্যুৎ কেন্দ্র (কাপ্তাই), কর্ণফুলী টানেল ও প্রথম বায়ুবিদ্যুৎ কেন্দ্র (ফেনী)",
      "হালদা প্রাকৃতিক মৎস্য প্রজনন ক্ষেত্র, তিতাস গ্যাসক্ষেত্র ও চাঁদপুরের ইলিশ (GI)"
    ],
    "examPointsEn": [
      "Largest division of Bangladesh by area (33,909 km²) with 11 districts",
      "Contains both the largest district (Rangamati) and least populous district (Bandarban)",
      "Home to Cox’s Bazar (120 km beach) and St. Martin’s (only coral island)",
      "Hosts Kaptai Hydroelectric Plant, Karnaphuli Tunnel & Feni wind plant",
      "Halda natural breeding ground, Titas Gas Field & Chandpur Hilsa (GI)"
    ]
  },
  "rajshahi": {
    "geoKey": "rajshahi",
    "nameBn": "রাজশাহী বিভাগ",
    "nameEn": "Rajshahi Division",
    "iso": "BD-E",
    "hqBn": "রাজশাহী",
    "hqEn": "Rajshahi",
    "established": "1829",
    "areaKm2": 18153,
    "population": 20353119,
    "density": 1121,
    "districtCount": 8,
    "upazilaCount": 68,
    "parliamentSeats": 39,
    "districts": [
      "rajshahi",
      "bogura",
      "pabna",
      "joypurhat",
      "sirajganj",
      "naogaon",
      "natore",
      "chapainawabganj"
    ],
    "govUrl": "https://rajshahidiv.gov.bd",
    "famousBn": "মহাস্থানগড়, পাহাড়পুর বৌদ্ধ বিহার (UNESCO), রূপপুর পারমাণবিক কেন্দ্র, চলনবিল, সিল্ক, আম, বগুড়ার দই",
    "famousEn": "Mahasthangarh, Paharpur Vihara (UNESCO), Rooppur NPP, Chalan Beel, Silk, Mangoes, Bogura Doi",
    "historyBn": "প্রাচীন বরেন্দ্র ও পুণ্ড্রবর্ধন সভ্যতার লীলাভূমি রাজশাহী বিভাগ ১৮২৯ সালে প্রতিষ্ঠিত হয়। ২০১০ সালে রংপুর পৃথক হওয়ার পর বর্তমানে ৮টি জেলা রয়েছে।",
    "historyEn": "Heartland of ancient Varendra and Pundravardhana; established in 1829. Comprises 8 districts after Rangpur was separated in 2010.",
    "examPointsBn": [
      "প্রাচীনতম নগরী মহাস্থানগড় (বগুড়া) ও UNESCO ঐতিহ্য পাহাড়পুর বৌদ্ধ বিহার (নওগাঁ)",
      "বাংলাদেশের প্রথম জাদুঘর বরেন্দ্র গবেষণা জাদুঘর (১৯১০) ও রাজশাহী বিশ্ববিদ্যালয় (১৯৫৩)",
      "রূপপুর পারমাণবিক বিদ্যুৎকেন্দ্র (পাবনা) ও হার্ডিঞ্জ ব্রিজ (১৯১৫)",
      "দেশের সর্বপশ্চিমের স্থান মনাকষা (শিবগঞ্জ, চাঁপাইনবাবগঞ্জ) ও বৃহত্তম বিল চলনবিল",
      "GI পণ্য: রাজশাহী সিল্ক, ফজলি ও ক্ষীরসাপাত আম, বগুড়ার দই, নাটোরের কাঁচাগোল্লা"
    ],
    "examPointsEn": [
      "Hosts Mahasthangarh (Bogura) & UNESCO World Heritage Paharpur Vihara (Naogaon)",
      "Varendra Research Museum (1910 — oldest museum) & Rajshahi University (1953)",
      "Rooppur Nuclear Power Plant & historic Hardinge Bridge (Pabna)",
      "Westernmost point of Bangladesh (Shibganj) & largest wetland Chalan Beel",
      "GI products: Rajshahi Silk, Fazli/Khirshapat Mango, Bogura Doi, Natore Kachagolla"
    ]
  },
  "khulna": {
    "geoKey": "khulna",
    "nameBn": "খুলনা বিভাগ",
    "nameEn": "Khulna Division",
    "iso": "BD-D",
    "hqBn": "খুলনা",
    "hqEn": "Khulna",
    "established": "1960",
    "areaKm2": 22274,
    "population": 17182100,
    "density": 771,
    "districtCount": 10,
    "upazilaCount": 59,
    "parliamentSeats": 36,
    "districts": [
      "jashore",
      "khulna",
      "kushtia",
      "satkhira",
      "jhenaidah",
      "bagerhat",
      "chuadanga",
      "magura",
      "narail",
      "meherpur"
    ],
    "govUrl": "https://khulnadiv.gov.bd",
    "famousBn": "সুন্দরবন (UNESCO), ষাট গম্বুজ মসজিদ (UNESCO), মোংলা বন্দর, বেনাপোল স্থলবন্দর, মুজিবনগর, শিলাইদহ, লালন আখড়া",
    "famousEn": "Sundarbans (UNESCO), Sixty Dome Mosque (UNESCO), Mongla Port, Benapole, Mujibnagar, Shilaidaha",
    "historyBn": "১৯৬০ সালের ১ অক্টোবর খুলনা বিভাগ প্রতিষ্ঠিত হয়। ১০টি জেলা নিয়ে গঠিত এই বিভাগে বাংলাদেশের ২টি UNESCO বিশ্ব ঐতিহ্যবাহী স্থান অবস্থিত।",
    "historyEn": "Established on 1 October 1960 with 10 districts; home to two of Bangladesh’s three UNESCO World Heritage Sites.",
    "examPointsBn": [
      "বাংলাদেশের দুটি UNESCO বিশ্ব ঐতিহ্য—সুন্দরবন (১৯৯৭) ও ষাট গম্বুজ মসজিদ (১৯৮৫) এই বিভাগে",
      "দেশের ২য় সমুদ্রবন্দর মোংলা (বাগেরহাট) ও বৃহত্তম স্থলবন্দর বেনাপোল (যশোর)",
      "মুজিবনগর (মেহেরপুর)—১৭ এপ্রিল ১৯৭১ প্রথম অস্থায়ী সরকারের শপথস্থান",
      "প্রথম শত্রুমুক্ত জেলা যশোর (৬ ডিসেম্বর ১৯৭১) ও প্রথম রেলপথ দর্শনা (১৮৬২)",
      "শিলাইদহ রবীন্দ্র কুঠিবাড়ি, লালন আখড়া (কুষ্টিয়া) ও ইসলামী বিশ্ববিদ্যালয় (১৯৭৯)"
    ],
    "examPointsEn": [
      "Home to two UNESCO Heritage Sites: Sundarbans (1997) & Sixty Dome Mosque (1985)",
      "Hosts Mongla (2nd seaport) & Benapole (largest land port)",
      "Mujibnagar (Meherpur) — oath site of 1st Provisional Govt (17 Apr 1971)",
      "First liberated district Jashore (6 Dec 1971) & first rail link Darshana (1862)",
      "Shilaidaha Kuthibari, Lalon Shrine & Islamic University (1979)"
    ]
  },
  "barishal": {
    "geoKey": "barishal",
    "nameBn": "বরিশাল বিভাগ",
    "nameEn": "Barishal Division",
    "iso": "BD-A",
    "hqBn": "বরিশাল",
    "hqEn": "Barishal",
    "established": "1993",
    "areaKm2": 13225,
    "population": 9100102,
    "density": 688,
    "districtCount": 6,
    "upazilaCount": 42,
    "parliamentSeats": 21,
    "districts": [
      "barishal",
      "patuakhali",
      "bhola",
      "pirojpur",
      "barguna",
      "jhalokati"
    ],
    "govUrl": "https://barisaldiv.gov.bd",
    "famousBn": "কুয়াকাটা সমুদ্রসৈকত, পায়রা সমুদ্রবন্দর, ভোলা দ্বীপ, ভাসমান পেয়ারা বাজার, ধানসিঁড়ি নদী, দুর্গাসাগর",
    "famousEn": "Kuakata Beach, Payra Seaport, Bhola Island, Floating Guava Market, Dhansiri River",
    "historyBn": "১৯৯৩ সালের ১ জানুয়ারি খুলনা বিভাগ থেকে আলাদা হয়ে ৬টি জেলা নিয়ে বরিশাল বিভাগ প্রতিষ্ঠিত হয়। নদীমাতৃক ঐতিহ্যের জন্য এটি \"ধান-নদী-খাল\" ও \"বাংলার শস্যভাণ্ডার\" নামে পরিচিত।",
    "historyEn": "Carved from Khulna Division on 1 January 1993 with 6 riverine districts; historically known as the Granary of Bengal.",
    "examPointsBn": [
      "১৯৯৩ সালের ১ জানুয়ারি ৬টি জেলা নিয়ে গঠিত (জনসংখ্যায় ক্ষুদ্রতম বিভাগ)",
      "কুয়াকাটা (পটুয়াখালী)—সূর্যোদয় ও সূর্যাস্ত দেখার একমাত্র সমুদ্রসৈকত",
      "পায়রা সমুদ্রবন্দর (দেশের ৩য় সমুদ্রবন্দর) ও পায়রা তাপবিদ্যুৎ কেন্দ্র",
      "ভোলা—বাংলাদেশের বৃহত্তম দ্বীপ ও একমাত্র দ্বীপ-জেলা (শাহবাজপুর গ্যাসক্ষেত্র)",
      "ঝালকাঠির ভীমরুলি ভাসমান পেয়ারা বাজার ও কবি জীবনানন্দ দাশের ধানসিঁড়ি নদী"
    ],
    "examPointsEn": [
      "Established on 1 Jan 1993 with 6 districts (least populous division)",
      "Kuakata (Patuakhali) — panoramic beach with both sunrise & sunset views",
      "Payra Seaport (3rd seaport of Bangladesh) & Payra Thermal Power Plant",
      "Bhola — largest island & only island district (Shahbazpur Gas Field)",
      "Bhimruli Floating Guava Market & Jibanananda Das’s Dhansiri River"
    ]
  },
  "sylhet": {
    "geoKey": "sylhet",
    "nameBn": "সিলেট বিভাগ",
    "nameEn": "Sylhet Division",
    "iso": "BD-G",
    "hqBn": "সিলেট",
    "hqEn": "Sylhet",
    "established": "1995",
    "areaKm2": 12635,
    "population": 11034863,
    "density": 873,
    "districtCount": 4,
    "upazilaCount": 41,
    "parliamentSeats": 19,
    "districts": [
      "sylhet",
      "sunamganj",
      "habiganj",
      "moulvibazar"
    ],
    "govUrl": "https://sylhetdiv.gov.bd",
    "famousBn": "চা বাগান, রাতারগুল সোয়াম্প ফরেস্ট, টাঙ্গুয়ার হাওর (রামসার), জাফলং, মাধবকুণ্ড, বিবিয়ানা ও হরিপুর গ্যাসক্ষেত্র",
    "famousEn": "Tea Gardens, Ratargul Swamp Forest, Tanguar Haor (Ramsar), Jaflong, Madhabkunda, Gas Fields",
    "historyBn": "১৯৯৫ সালের ১ আগস্ট চট্টগ্রাম বিভাগ থেকে পৃথক হয়ে ৪টি জেলা নিয়ে সিলেট বিভাগ প্রতিষ্ঠিত হয়। চা, প্রাকৃতিক গ্যাস, হাওর ও প্রবাসী রেমিট্যান্সের জন্য অনন্য।",
    "historyEn": "Separated from Chattogram Division on 1 August 1995 with 4 districts; renowned for tea, natural gas, wetlands, and diaspora remittances.",
    "examPointsBn": [
      "১৯৯৫ সালের ১ আগস্ট ৪টি জেলা নিয়ে গঠিত (জেলা সংখ্যায় ক্ষুদ্রতমগুলোর একটি)",
      "টাঙ্গুয়ার হাওর (২য় রামসার সাইট, সুনামগঞ্জ) ও হাকালুকি হাওর (দেশের বৃহত্তম হাওর)",
      "বাংলাদেশের প্রথম চা বাগান মালনীছড়া (১৮৫৪) ও প্রথম গ্যাসক্ষেত্র হরিপুর (১৯৫৫)",
      "রাতারগুল একমাত্র মিঠাপানির জলাবন ও বাংলাদেশের সর্বাধিক বৃষ্টিপাত অঞ্চল",
      "তেলিয়াপাড়া (হবিগঞ্জ)—৪ এপ্রিল ১৯৭১ মুক্তিযুদ্ধের প্রথম সেনা সদর বৈঠক"
    ],
    "examPointsEn": [
      "Formed on 1 August 1995 with 4 districts",
      "Tanguar Haor (2nd Ramsar Site) & Hakaluki Haor (largest haor)",
      "Malnicherra (1st tea garden, 1854) & Haripur (1st gas field, 1955)",
      "Ratargul (only freshwater swamp forest) & Bangladesh’s highest rainfall zone",
      "Teliapara (Habiganj) — 4 April 1971 Liberation War command meeting"
    ]
  },
  "rangpur": {
    "geoKey": "rangpur",
    "nameBn": "রংপুর বিভাগ",
    "nameEn": "Rangpur Division",
    "iso": "BD-F",
    "hqBn": "রংপুর",
    "hqEn": "Rangpur",
    "established": "2010",
    "areaKm2": 16185,
    "population": 18610956,
    "density": 1150,
    "districtCount": 8,
    "upazilaCount": 58,
    "parliamentSeats": 33,
    "districts": [
      "rangpur",
      "nilphamari",
      "dinajpur",
      "thakurgaon",
      "gaibandha",
      "kurigram",
      "lalmonirhat",
      "panchagarh"
    ],
    "govUrl": "https://rangpurdiv.gov.bd",
    "famousBn": "বাংলাবান্ধা, তিস্তা ব্যারেজ, কান্তজিউ মন্দির, বড়পুকুরিয়া কয়লাখনি, সমতলের চা, শতরঞ্জি ও হাঁড়িভাঙ্গা আম",
    "famousEn": "Banglabandha, Teesta Barrage, Kantajew Temple, Barapukuria Coal Mine, Plain-land Tea, Shataranji",
    "historyBn": "২০১০ সালের ২৫ জানুয়ারি রাজশাহী বিভাগ থেকে আলাদা হয়ে ৮টি জেলা নিয়ে রংপুর ৭ম বিভাগ হিসেবে গঠিত হয়।",
    "historyEn": "Established on 25 January 2010 as Bangladesh’s 7th division with 8 northern districts carved from Rajshahi.",
    "examPointsBn": [
      "২০১০ সালের ২৫ জানুয়ারি ৮টি জেলা নিয়ে বাংলাদেশের ৭ম বিভাগ হিসেবে প্রতিষ্ঠিত",
      "সর্বউত্তরের স্থান বাংলাবান্ধা (তেঁতুলিয়া, পঞ্চগড়) ও চতুর্দেশীয় স্থলবন্দর",
      "বড়পুকুরিয়া কয়লাখনি ও মধ্যপাড়া কঠিন শিলা খনি (দিনাজপুর)",
      "তিস্তা ব্যারেজ (বৃহত্তম সেচ প্রকল্প), তিন বিঘা করিডোর ও সৈয়দপুর রেলওয়ে কারখানা",
      "কান্তজিউ মন্দির, বেগম রোকেয়ার জন্মস্থান পায়রাবন্দ ও GI পণ্য শতরঞ্জি/হাঁড়িভাঙ্গা/কাটারিভোগ"
    ],
    "examPointsEn": [
      "Established on 25 Jan 2010 as Bangladesh’s 7th division (8 districts)",
      "Northernmost point Banglabandha (Tetulia, Panchagarh) & 4-nation transit port",
      "Barapukuria Coal Mine & Maddhapara Hard Rock Mine (Dinajpur)",
      "Teesta Barrage (largest irrigation project), Tin Bigha Corridor & Saidpur Rail Workshop",
      "Kantajew Temple, Begum Rokeya’s Pairaband & GI products (Shataranji, Haribhanga, Kataribhog)"
    ]
  },
  "mymensingh": {
    "geoKey": "mymensingh",
    "nameBn": "ময়মনসিংহ বিভাগ",
    "nameEn": "Mymensingh Division",
    "iso": "BD-H",
    "hqBn": "ময়মনসিংহ",
    "hqEn": "Mymensingh",
    "established": "2015",
    "areaKm2": 10669,
    "population": 12225498,
    "density": 1146,
    "districtCount": 4,
    "upazilaCount": 35,
    "parliamentSeats": 24,
    "districts": [
      "mymensingh",
      "jamalpur",
      "netrokona",
      "sherpur"
    ],
    "govUrl": "https://mymensinghdiv.gov.bd",
    "famousBn": "বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU), বিরিশিরি চিনামাটির পাহাড়, গারো পাহাড়, নকশিকাঁথা, বালিশ মিষ্টি, তুলশীমালা চাল",
    "famousEn": "Bangladesh Agricultural University (BAU), Birishiri White Clay Hills, Garo Hills, Nakshi Kantha",
    "historyBn": "২০১৫ সালের ১৪ সেপ্টেম্বর ঢাকা বিভাগ থেকে পৃথক হয়ে ৪টি জেলা নিয়ে বাংলাদেশের ৮ম ও সর্বশেষ বিভাগ হিসেবে ময়মনসিংহ গঠিত হয়।",
    "historyEn": "Formed on 14 September 2015 from Dhaka Division with 4 districts as Bangladesh’s 8th and newest division.",
    "examPointsBn": [
      "বাংলাদেশের ৮ম ও সর্বশেষ বিভাগ (১৪ সেপ্টেম্বর ২০১৫, ৪টি জেলা) এবং আয়তনে ক্ষুদ্রতম বিভাগ",
      "বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU), BFRI ও BINA-র প্রধান কার্যালয়",
      "বিরিশিরি (বিজয়পুর, নেত্রকোণা)—দেশের প্রধান চিনামাটি/সাদামাটির খনি",
      "GI পণ্য: জামালপুরের নকশিকাঁথা, নেত্রকোণার বালিশ মিষ্টি, মুক্তাগাছার মণ্ডা ও শেরপুরের তুলশীমালা চাল",
      "পুরাতন ব্রহ্মপুত্র ও সোমেশ্বরী নদী এবং মৈমনসিংহ গীতিকা (লোকসাহিত্য)"
    ],
    "examPointsEn": [
      "Bangladesh’s 8th and newest division (14 Sep 2015, 4 districts) & smallest by area",
      "Headquarters of Bangladesh Agricultural University (BAU), BFRI & BINA",
      "Birishiri / Bijoypur (Netrokona) — major white china-clay hills",
      "GI products: Jamalpur Nakshi Kantha, Balish Mishti, Muktagachha Monda & Tulshimala Rice",
      "Old Brahmaputra & Someshwari rivers + Maimansingha Gitika folk heritage"
    ]
  }
};

export const BD_DISTRICTS = {
  "dhaka": {
    "geoKey": "dhaka",
    "divisionKey": "dhaka",
    "nameBn": "ঢাকা",
    "nameEn": "Dhaka",
    "established": "1772",
    "areaKm2": 1464,
    "population": 14734701,
    "density": 10065,
    "parliamentSeats": 20,
    "upazilaCount": 5,
    "govUrl": "https://dhaka.gov.bd",
    "famousBn": "জামদানি, মসলিন ঐতিহ্য, লালবাগ কেল্লা, আহসান মঞ্জিল, কেন্দ্রীয় শহীদ মিনার, জাতীয় সংসদ ভবন",
    "famousEn": "Jamdani, Muslin heritage, Lalbagh Fort, Ahsan Manzil, Central Shaheed Minar, National Parliament",
    "historyBn": "১৬১০ সালে সুবেদার ইসলাম খান চিশতী ঢাকাকে বাংলার রাজধানী (জাহাঙ্গীরনগর) ঘোষণা করেন। ১৭৭২ সালে ঢাকা জেলা প্রতিষ্ঠিত হয় এবং ১৯৭১ সালে স্বাধীন বাংলাদেশের রাজধানী হয়।",
    "historyEn": "Declared capital of Bengal (Jahangirnagar) by Subahdar Islam Khan in 1610. Established as a district in 1772 and became the capital of independent Bangladesh in 1971.",
    "examPointsBn": [
      "বাংলাদেশের সর্বাধিক জনবহুল জেলা ও জনসংখ্যার ঘনত্ব সর্বাধিক",
      "১৬১০ সালে ইসলাম খান কর্তৃক বাংলার রাজধানী স্থাপন",
      "ঢাকা বিশ্ববিদ্যালয় (১৯২১), বুয়েট (১৯৬২) ও কেন্দ্রীয় শহীদ মিনার",
      "সাভারে জাতীয় স্মৃতিসৌধ (স্থপতি: সৈয়দ মাইনুল হোসেন)"
    ],
    "examPointsEn": [
      "Most populous district & highest population density in Bangladesh",
      "Made Bengal capital in 1610 by Subahdar Islam Khan",
      "Home to University of Dhaka (1921), BUET (1962) & Shaheed Minar",
      "National Martyrs’ Memorial at Savar (Architect: Syed Mainul Hossain)"
    ]
  },
  "faridpur": {
    "geoKey": "faridpur",
    "divisionKey": "dhaka",
    "nameBn": "ফরিদপুর",
    "nameEn": "Faridpur",
    "established": "1786",
    "areaKm2": 2053,
    "population": 2162876,
    "density": 1054,
    "parliamentSeats": 4,
    "upazilaCount": 9,
    "govUrl": "https://faridpur.gov.bd",
    "famousBn": "খেজুরের গুড়, পাট চাষ, পল্লীকবি জসীমউদ্দীনের বাড়ি (অম্বিকাপুর), নদী গবেষণা ইনস্টিটিউট",
    "famousEn": "Date palm jaggery, Jute production, Poet Jasimuddin’s home, River Research Institute",
    "historyBn": "সুফি সাধক শাহ শেখ ফরিদউদ্দীনের নামে নামকরণকৃত ফরিদপুর জেলা ১৭৮৬ সালে প্রতিষ্ঠিত হয়।",
    "historyEn": "Named after Sufi saint Shah Sheikh Fariduddin; established as a district in 1786.",
    "examPointsBn": [
      "বাংলাদেশের সর্বাধিক পাট উৎপাদনকারী জেলা",
      "নদী গবেষণা ইনস্টিটিউট (RRI) অবস্থিত",
      "পল্লীকবি জসীমউদ্দীনের জন্মস্থান (তাম্বুলখানা)",
      "পদ্মা, আড়িয়াল খাঁ ও কুমার নদী প্রবাহিত"
    ],
    "examPointsEn": [
      "Highest jute-producing district in Bangladesh",
      "Home to the River Research Institute (RRI)",
      "Birthplace of Pastoral Poet Jasimuddin",
      "Traversed by Padma, Arial Khan & Kumar rivers"
    ]
  },
  "tangail": {
    "geoKey": "tangail",
    "divisionKey": "dhaka",
    "nameBn": "টাঙ্গাইল",
    "nameEn": "Tangail",
    "established": "1969",
    "areaKm2": 3414,
    "population": 4037608,
    "density": 1183,
    "parliamentSeats": 8,
    "upazilaCount": 12,
    "govUrl": "https://tangail.gov.bd",
    "famousBn": "টাঙ্গাইল তাঁত শাড়ি (GI পণ্য), পোড়াবাড়ির চমচম (GI পণ্য), মধুপুর জাতীয় উদ্যান, আতিয়া মসজিদ",
    "famousEn": "Tangail Handloom Saree (GI), Porabari Chomchom (GI), Madhupur National Park, Atia Mosque",
    "historyBn": "ময়মনসিংহ থেকে আলাদা হয়ে ১৯৬৯ সালে টাঙ্গাইল জেলা প্রতিষ্ঠিত হয়। যমুনা নদীর তীরে অবস্থিত এই জেলা তাঁতশিল্প ও মধুপুর গড়ের জন্য বিখ্যাত।",
    "historyEn": "Carved out of Mymensingh in 1969. Renowned for handloom sarees, Madhupur Sal forest tract, and Jamuna riverfront.",
    "examPointsBn": [
      "টাঙ্গাইল শাড়ি ও পোড়াবাড়ির চমচম বাংলাদেশের স্বীকৃত GI পণ্য",
      "মধুপুর গড় (প্লাইস্টোসিন কালের সোপান) ও জাতীয় উদ্যান",
      "যমুনা বহুমুখী সেতুর পূর্ব প্রান্ত (ভূঞাপুর)",
      "মাওলানা ভাসানী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (MBSTU) ও আতিয়া মসজিদ (১০ টাকার নোটে ছিল)"
    ],
    "examPointsEn": [
      "Tangail Saree & Porabari Chomchom are recognized GI products",
      "Madhupur Tract (Pleistocene terrace) & National Park",
      "Eastern end of Jamuna Multipurpose Bridge",
      "Mawlana Bhashani Science & Technology University (MBSTU) & Atia Mosque"
    ]
  },
  "gazipur": {
    "geoKey": "gazipur",
    "divisionKey": "dhaka",
    "nameBn": "গাজীপুর",
    "nameEn": "Gazipur",
    "established": "1984",
    "areaKm2": 1806,
    "population": 5263474,
    "density": 2914,
    "parliamentSeats": 5,
    "upazilaCount": 5,
    "govUrl": "https://gazipur.gov.bd",
    "famousBn": "ভাওয়াল জাতীয় উদ্যান, বঙ্গবন্ধু শেখ মুজিব সাফারি পার্ক, বাংলাদেশ ধান গবেষণা ইনস্টিটিউট (BRRI), কৃষি গবেষণা (BARI)",
    "famousEn": "Bhawal National Park, Safari Park, BRRI, BARI, National University, IUT, DUET",
    "historyBn": "ভাওয়াল পরগনার ঐতিহাসিক কেন্দ্র গাজীপুর ১৯৮৪ সালে জেলা হিসেবে প্রতিষ্ঠিত হয়। এটি বাংলাদেশের প্রধান শিল্পাঞ্চল ও কৃষি গবেষণার প্রাণকেন্দ্র।",
    "historyEn": "Historic seat of Bhawal Pargana, established as a district in 1984. Major industrial belt and national agricultural research hub.",
    "examPointsBn": [
      "জাতীয় বিশ্ববিদ্যালয় (১৯৯২), উন্মুক্ত বিশ্ববিদ্যালয় (BOU), ডুয়েট (DUET) ও IUT অবস্থিত",
      "বাংলাদেশ ধান গবেষণা ইনস্টিটিউট (BRRI) ও BARI-র প্রধান কার্যালয়",
      "১৯৭১ সালের ১৯ মার্চ প্রথম সশস্ত্র প্রতিরোধ (জয়দেবপুর)",
      "ভাওয়াল জাতীয় উদ্যান ও টঙ্গী শিল্পাঞ্চল"
    ],
    "examPointsEn": [
      "Hosts National University (1992), BOU, DUET & IUT",
      "Headquarters of BRRI and BARI agricultural research institutes",
      "Site of first armed resistance on 19 March 1971 at Joydebpur",
      "Bhawal National Park & Tongi industrial zone"
    ]
  },
  "narayanganj": {
    "geoKey": "narayanganj",
    "divisionKey": "dhaka",
    "nameBn": "নারায়ণগঞ্জ",
    "nameEn": "Narayanganj",
    "established": "1984",
    "areaKm2": 684,
    "population": 3909138,
    "density": 5712,
    "parliamentSeats": 5,
    "upazilaCount": 5,
    "govUrl": "https://narayanganj.gov.bd",
    "famousBn": "সোনারগাঁও, পানাম নগর, ঢাকাই জামদানি (GI পণ্য), প্রাচ্যের ডান্ডি, শীতলক্ষ্যা নদীবন্দর",
    "famousEn": "Sonargaon, Panam City, Dhakai Jamdani (GI), Dundee of the East, Shitalakshya River Port",
    "historyBn": "প্রাচীন বাংলার রাজধানী সোনারগাঁও এবং পাট বাণিজ্যের জন্য খ্যাত \"প্রাচ্যের ডান্ডি\" নারায়ণগঞ্জ ১৯৮৪ সালে জেলায় উন্নীত হয়।",
    "historyEn": "Includes ancient Bengal capital Sonargaon and historic jute trade hub known as the \"Dundee of the East\"; became a district in 1984.",
    "examPointsBn": [
      "বাংলাদেশের আয়তনে ক্ষুদ্রতম জেলা (৬৮৪ বর্গ কি.মি.)",
      "বাংলার প্রাচীন রাজধানী সোনারগাঁও ও লোক ও কারুশিল্প জাদুঘর",
      "ঢাকাই জামদানি (বাংলাদেশের ১ম GI পণ্য, ২০১৬) ও পানাম নগর",
      "পাটশিল্পের জন্য \"প্রাচ্যের ডান্ডি\" নামে পরিচিত"
    ],
    "examPointsEn": [
      "Smallest district of Bangladesh by area (684 km²)",
      "Ancient capital Sonargaon & Folk Arts and Crafts Museum",
      "Dhakai Jamdani (Bangladesh’s 1st GI product, 2016) & Panam City",
      "Historically known as the \"Dundee of the East\" for jute trade"
    ]
  },
  "kishoreganj": {
    "geoKey": "kishoreganj",
    "divisionKey": "dhaka",
    "nameBn": "কিশোরগঞ্জ",
    "nameEn": "Kishoreganj",
    "established": "1984",
    "areaKm2": 2689,
    "population": 3267630,
    "density": 1215,
    "parliamentSeats": 6,
    "upazilaCount": 13,
    "govUrl": "https://kishoreganj.gov.bd",
    "famousBn": "নিকলী-ইটনা-মিঠামইন হাওর, শোলাকিয়া ঈদগাহ, জঙ্গলবাড়ি (ঈশা খাঁর দুর্গ), পাগলা মসজিদ, পনির",
    "famousEn": "Nikli-Itna-Mithamain Haor, Sholakia Eidgah, Jangalbari Fort (Isa Khan), Pagla Mosque",
    "historyBn": "ময়মনসিংহ থেকে পৃথক হয়ে ১৯৮৪ সালে কিশোরগঞ্জ জেলা গঠিত হয়। ভাটি অঞ্চলের হাওর ও বারো ভূঁইয়া প্রধান ঈশা খাঁর স্মৃতিবিজড়িত।",
    "historyEn": "Formed in 1984 from Mymensingh. Famous for expansive wetlands (haors) and Isa Khan’s Jangalbari Fort.",
    "examPointsBn": [
      "বাংলাদেশের বৃহত্তম ঐতিহাসিক ঈদগাহ ময়দান—শোলাকিয়া",
      "বারো ভূঁইয়াদের প্রধান ঈশা খাঁর রাজধানী জঙ্গলবাড়ি",
      "ইটনা-মিঠামইন-অষ্টগ্রাম অল-ওয়েদার সড়ক ও হাওর অঞ্চল",
      "আশ্বিনের মহাষ্টমী ও অষ্টগ্রামের পনির (GI পণ্য)"
    ],
    "examPointsEn": [
      "Sholakia — one of the largest historic Eidgah grounds",
      "Jangalbari Fort, capital of Baro-Bhuyan leader Isa Khan",
      "Itna-Mithamain-Austagram all-weather haor highway",
      "Austagram Cheese (GI product)"
    ]
  },
  "narsingdi": {
    "geoKey": "narsingdi",
    "divisionKey": "dhaka",
    "nameBn": "নরসিংদী",
    "nameEn": "Narsingdi",
    "established": "1984",
    "areaKm2": 1150,
    "population": 2584452,
    "density": 2247,
    "parliamentSeats": 5,
    "upazilaCount": 6,
    "govUrl": "https://narsingdi.gov.bd",
    "famousBn": "উয়ারী-বটেশ্বর (২৫০০ বছরের প্রাচীন দুর্গনগরী), বাবুরহাট বস্ত্র বাজার, সাগর কলা (GI), লটকন",
    "famousEn": "Wari-Bateshwar (2,500-yr ancient site), Baburhat textile market, Sagar Banana (GI), Lotkon",
    "historyBn": "১৯৮৪ সালে জেলা হিসেবে প্রতিষ্ঠিত নরসিংদীতে বাংলাদেশের প্রাচীনতম প্রত্নতাত্ত্বিক নগরী উয়ারী-বটেশ্বর অবস্থিত।",
    "historyEn": "Established in 1984; home to Wari-Bateshwar, the oldest known fortified archaeological city in Bangladesh.",
    "examPointsBn": [
      "উয়ারী-বটেশ্বর (বেলাব)—খ্রিস্টপূর্ব ৪৫০ অব্দের প্রাচীন নগরসভ্যতা ও ছাপাঙ্কিত রৌপ্যমুদ্রা",
      "বীরশ্রেষ্ঠ ফ্লাইট লেফটেন্যান্ট মতিউর রহমানের জন্মস্থান (রায়পুরা)",
      "নরসিংদীর অমৃতসাগর কলা (GI পণ্য) ও বাবুরহাট পাইকারি কাপড়ের হাট"
    ],
    "examPointsEn": [
      "Wari-Bateshwar archaeological site (~450 BCE punch-marked coins)",
      "Birthplace of Bir Sreshtho Flight Lt. Matiur Rahman (Raipura)",
      "Amrit Sagar Banana (GI product) & Baburhat wholesale textile hub"
    ]
  },
  "munshiganj": {
    "geoKey": "munshiganj",
    "divisionKey": "dhaka",
    "nameBn": "মুন্সীগঞ্জ",
    "nameEn": "Munshiganj",
    "established": "1984",
    "areaKm2": 1004,
    "population": 1625418,
    "density": 1618,
    "parliamentSeats": 3,
    "upazilaCount": 6,
    "govUrl": "https://munshiganj.gov.bd",
    "famousBn": "বিক্রমপুর, পদ্মা সেতুর উত্তর প্রান্ত (মাওয়া), আলু উৎপাদন, অতীশ দীপঙ্করের জন্মস্থান, ইদ্রাকপুর দুর্গ",
    "famousEn": "Bikrampur, Padma Bridge north end (Mawa), Potato hub, Atisha Dipankara birthplace, Idrakpur Fort",
    "historyBn": "প্রাচীন বঙ্গের প্রাণকেন্দ্র বিক্রমপুর (মুন্সীগঞ্জ) ১৯৮৪ সালে জেলা হয়। জ্ঞানতাপস অতীশ দীপঙ্কর শ্রীজ্ঞান এখানেই জন্মগ্রহণ করেন।",
    "historyEn": "Heart of ancient Bikrampur; formed as a district in 1984. Birthplace of Buddhist scholar Atisha Dipankara.",
    "examPointsBn": [
      "পদ্মা সেতুর উত্তর প্রান্ত (মাওয়া, লৌহজং)",
      "বাংলাদেশের সর্বাধিক আলু উৎপাদনকারী জেলা",
      "বৌদ্ধ পণ্ডিত অতীশ দীপঙ্কর শ্রীজ্ঞান (বজ্রযোগিনী) ও বিজ্ঞানী জগদীশ চন্দ্র বসুর পৈতৃক নিবাস",
      "ইদ্রাকপুর জলদুর্গ (মীর জুমলা নির্মিত, ১৬৬০) ও আড়িয়াল বিল"
    ],
    "examPointsEn": [
      "Northern end of Padma Multipurpose Bridge (Mawa, Louhajang)",
      "Top potato-producing district in Bangladesh",
      "Birthplace of Atisha Dipankara & ancestral home of Sir Jagadish Chandra Bose",
      "Idrakpur River Fort (1660) & Arial Beel wetland"
    ]
  },
  "manikganj": {
    "geoKey": "manikganj",
    "divisionKey": "dhaka",
    "nameBn": "মানিকগঞ্জ",
    "nameEn": "Manikganj",
    "established": "1984",
    "areaKm2": 1384,
    "population": 1558024,
    "density": 1126,
    "parliamentSeats": 3,
    "upazilaCount": 7,
    "govUrl": "https://manikganj.gov.bd",
    "famousBn": "বালিয়াটি জমিদার বাড়ি, আরিচা-পাটুরিয়া ফেরিঘাট, হাজারী গুড়, ধলেশ্বরী ও পদ্মা নদী",
    "famousEn": "Baliati Zamindar Palace, Aricha-Paturia Ferry Ghat, Hazari Gur",
    "historyBn": "সুফি মানিক শাহের নামে নামকরণকৃত মানিকগঞ্জ ১৯৮৪ সালে ঢাকা থেকে পৃথক জেলা হয়।",
    "historyEn": "Named after Sufi saint Manik Shah; became a separate district from Dhaka in 1984.",
    "examPointsBn": [
      "বালিয়াটি জমিদার বাড়ি (বাংলাদেশের অন্যতম বৃহৎ জমিদার বাড়ি)",
      "পাটুরিয়া-দৌলতদিয়া নৌরুট ও যমুনা-পদ্মার মিলনস্থল (আরিচা)",
      "ভাষা শহীদ রফিকউদ্দিন আহমেদের জন্মস্থান (পারিল বলধারা)"
    ],
    "examPointsEn": [
      "Baliati Zamindar Palace — one of Bangladesh’s largest heritage mansions",
      "Paturia ferry terminal & Jamuna-Padma confluence near Aricha",
      "Birthplace of Language Martyr Rafiq Uddin Ahmed"
    ]
  },
  "gopalganj": {
    "geoKey": "gopalganj",
    "divisionKey": "dhaka",
    "nameBn": "গোপালগঞ্জ",
    "nameEn": "Gopalganj",
    "established": "1984",
    "areaKm2": 1469,
    "population": 1295053,
    "density": 882,
    "parliamentSeats": 3,
    "upazilaCount": 5,
    "govUrl": "https://gopalganj.gov.bd",
    "famousBn": "টুঙ্গিপাড়া, বশেমুরবিপ্রবি (BSMRSTU), ওড়াকান্দি ঠাকুরবাড়ি, মধুমতী নদী",
    "famousEn": "Tungipara, BSMRSTU, Orakandi, Madhumati River",
    "historyBn": "ফরিদপুর জেলার মহকুমা থেকে ১৯৮৪ সালে গোপালগঞ্জ পূর্ণাঙ্গ জেলা হিসেবে আত্মপ্রকাশ করে।",
    "historyEn": "Upgraded from a subdivision of Faridpur to a full district in 1984.",
    "examPointsBn": [
      "মধুমতী নদী প্রবাহিত ও দক্ষিণ-পশ্চিম অঞ্চলের ঐতিহাসিক জেলা",
      "বঙ্গবন্ধু শেখ মুজিবুর রহমান বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় অবস্থিত",
      "ওড়াকান্দি মতুয়া সম্প্রদায়ের তীর্থস্থান"
    ],
    "examPointsEn": [
      "Traversed by the Madhumati River in south-central Bangladesh",
      "Hosts BSMRSTU university",
      "Orakandi pilgrimage site"
    ]
  },
  "shariatpur": {
    "geoKey": "shariatpur",
    "divisionKey": "dhaka",
    "nameBn": "শরীয়তপুর",
    "nameEn": "Shariatpur",
    "established": "1984",
    "areaKm2": 1174,
    "population": 1294561,
    "density": 1103,
    "parliamentSeats": 3,
    "upazilaCount": 6,
    "govUrl": "https://shariatpur.gov.bd",
    "famousBn": "পদ্মা সেতুর জাজিরা প্রান্ত, হাজী শরীয়তুল্লাহর স্মৃতি, পদ্মা ও মেঘনা নদী",
    "famousEn": "Padma Bridge Jajira end, Haji Shariatullah heritage, Padma & Meghna rivers",
    "historyBn": "ফরায়েজি আন্দোলনের প্রবক্তা হাজী শরীয়তুল্লাহর নামে নামকরণকৃত জেলাটি ১৯৮৪ সালে প্রতিষ্ঠিত হয়।",
    "historyEn": "Named after Faraizi Movement leader Haji Shariatullah; established as a district in 1984.",
    "examPointsBn": [
      "ফরায়েজি আন্দোলনের নেতা হাজী শরীয়তুল্লাহর নামানুসারে নামকরণ",
      "পদ্মা বহুমুখী সেতুর জাজিরা প্রান্ত শরীয়তপুর জেলায়",
      "পদ্মা ও মেঘনা নদীর মোহনা সংলগ্ন জেলা"
    ],
    "examPointsEn": [
      "Named after Faraizi Movement pioneer Haji Shariatullah",
      "Hosts the Jajira southern end of Padma Bridge",
      "Located near the Padma-Meghna confluence"
    ]
  },
  "madaripur": {
    "geoKey": "madaripur",
    "divisionKey": "dhaka",
    "nameBn": "মাদারীপুর",
    "nameEn": "Madaripur",
    "established": "1984",
    "areaKm2": 1125,
    "population": 1293027,
    "density": 1149,
    "parliamentSeats": 3,
    "upazilaCount": 5,
    "govUrl": "https://madaripur.gov.bd",
    "famousBn": "পদ্মা সেতুর দক্ষিণ প্রান্ত (শিবচর), শকুনি লেক, আড়িয়াল খাঁ নদী, খেজুর গুড়",
    "famousEn": "Padma Bridge south end (Shibchar), Shakuni Lake, Arial Khan River",
    "historyBn": "পঞ্চদশ শতাব্দীর সুফি সাধক বদিউদ্দিন আহমেদ জিন্দা শাহ মাদারের নামে নামকরণ; ১৯৮৪ সালে জেলা হয়।",
    "historyEn": "Named after 15th-century Sufi saint Shah Madar; became a district in 1984.",
    "examPointsBn": [
      "পদ্মা বহুমুখী সেতুর দক্ষিণ প্রান্ত (জাজিরা-শিবচর সংযোগ)",
      "আড়িয়াল খাঁ ও কুমার নদী প্রবাহিত",
      "শকুনি লেক ও রাজা রাম রায়ের বাড়ি"
    ],
    "examPointsEn": [
      "Southern approach corridor of Padma Multipurpose Bridge (Shibchar)",
      "Arial Khan & Kumar rivers flow through the district",
      "Shakuni Lake & historic heritage sites"
    ]
  },
  "rajbari": {
    "geoKey": "rajbari",
    "divisionKey": "dhaka",
    "nameBn": "রাজবাড়ী",
    "nameEn": "Rajbari",
    "established": "1984",
    "areaKm2": 1092,
    "population": 1189821,
    "density": 1089,
    "parliamentSeats": 2,
    "upazilaCount": 5,
    "govUrl": "https://rajbari.gov.bd",
    "famousBn": "দৌলতদিয়া ঘাট, মীর মশাররফ হোসেন স্মৃতিকেন্দ্র (পদমদী), পদ্মা ও গড়াই নদী",
    "famousEn": "Daulatdia Ghat, Mir Mosharraf Hossain Memorial, Padma & Gorai rivers",
    "historyBn": "ফরিদপুরের গোয়ালন্দ মহকুমা থেকে ১৯৮৪ সালে রাজবাড়ী জেলা গঠিত হয়।",
    "historyEn": "Formed from Goalanda subdivision of Faridpur in 1984.",
    "examPointsBn": [
      "গোয়ালন্দ ঘাটে পদ্মা ও যমুনার মিলনস্থল",
      "‘বিষাদ-সিন্ধু’ রচয়িতা মীর মশাররফ হোসেনের স্মৃতিকেন্দ্র (বালিয়াকান্দি)",
      "দৌলতদিয়া-পাটুরিয়া দেশের অন্যতম ব্যস্ত ফেরিঘাট"
    ],
    "examPointsEn": [
      "Padma-Jamuna confluence near Goalanda",
      "Mir Mosharraf Hossain (author of Bishad Shindhu) Memorial at Baliakandi",
      "Daulatdia-Paturia major river ferry crossing"
    ]
  },
  "chattogram": {
    "geoKey": "chattogram",
    "divisionKey": "chattogram",
    "nameBn": "চট্টগ্রাম",
    "nameEn": "Chattogram",
    "established": "1666",
    "areaKm2": 5283,
    "population": 9169464,
    "density": 1736,
    "parliamentSeats": 16,
    "upazilaCount": 15,
    "govUrl": "https://chattogram.gov.bd",
    "famousBn": "চট্টগ্রাম সমুদ্রবন্দর, বাণিজ্যিক রাজধানী, কর্ণফুলী টানেল, হালদা নদী, চট্টগ্রাম বিশ্ববিদ্যালয়, সীতাকুণ্ড, পতেঙ্গা",
    "famousEn": "Chattogram Seaport, Commercial Capital, Karnaphuli Tunnel, Halda River, CU, Sitakunda",
    "historyBn": "১৬৬৬ সালে শায়েস্তা খান চট্টগ্রাম জয় করে ইসলামাবাদ নাম রাখেন। এটি বাংলাদেশের বাণিজ্যিক রাজধানী এবং দেশের বৃহত্তম সমুদ্রবন্দর।",
    "historyEn": "Conquered by Shaista Khan in 1666 (named Islamabad). Commercial capital of Bangladesh and home to its largest seaport.",
    "examPointsBn": [
      "দেশের প্রধান সমুদ্রবন্দর (চট্টগ্রাম বন্দর) ও বাণিজ্যিক রাজধানী",
      "বঙ্গবন্ধু শেখ মুজিবুর রহমান টানেল (কর্ণফুলী নদীর তলদেশে দক্ষিণ এশিয়ার প্রথম রোড টানেল)",
      "হালদা নদী—এশিয়ার একমাত্র প্রাকৃতিক মৎস্য প্রজনন ক্ষেত্র",
      "বাংলাদেশ নেভাল একাডেমি, বিএমএ (ভাটিয়ারী), চবি (১৯৬৬) ও চুয়েট"
    ],
    "examPointsEn": [
      "Bangladesh’s primary seaport & commercial capital",
      "Karnaphuli Tunnel — South Asia’s first underwater road tunnel",
      "Halda River — Asia’s only natural carp breeding ground",
      "Hosts University of Chittagong (1966), CUET, BMA & Naval Academy"
    ]
  },
  "cumilla": {
    "geoKey": "cumilla",
    "divisionKey": "chattogram",
    "nameBn": "কুমিল্লা",
    "nameEn": "Cumilla",
    "established": "1790",
    "areaKm2": 3146,
    "population": 6212216,
    "density": 1974,
    "parliamentSeats": 11,
    "upazilaCount": 17,
    "govUrl": "https://comilla.gov.bd",
    "famousBn": "ময়নামতি, শালবন বিহার, রসমালাই (GI পণ্য), খাদি কাপড়, বার্ড (BARD), কুমিল্লা বিশ্ববিদ্যালয়",
    "famousEn": "Mainamati, Shalban Vihara, Rasmalai (GI), Khadi fabric, BARD, Comilla University",
    "historyBn": "প্রাচীন সমতট জনপদের প্রাণকেন্দ্র কুমিল্লা ১৭৯০ সালে ত্রিপুরা জেলা হিসেবে প্রতিষ্ঠিত হয়। ১৯৬০ সালে কুমিল্লা নামকরণ হয়।",
    "historyEn": "Center of ancient Samatata janapada; established as Tippera district in 1790 and renamed Comilla in 1960.",
    "examPointsBn": [
      "প্রাচীন বৌদ্ধ বিহার—শালবন বিহার ও ময়নামতি প্রত্নতাত্ত্বিক নিদর্শন",
      "কুমিল্লার রসমালাই (GI পণ্য) ও খাদি শিল্প",
      "বাংলাদেশ পল্লী উন্নয়ন একাডেমি (BARD, ১৯৫৯—ড. আখতার হামিদ খান)",
      "দ্বিতীয় বিশ্বযুদ্ধের ময়নামতি ওয়ার সিমেট্রি ও গোমতী নদী"
    ],
    "examPointsEn": [
      "Shalban Vihara & Mainamati ancient Buddhist archaeological sites",
      "Cumilla Rasmalai (GI product) & Khadi handloom",
      "BARD (1959, founded by Dr. Akhter Hameed Khan)",
      "Mainamati WWII War Cemetery & Gumti River"
    ]
  },
  "noakhali": {
    "geoKey": "noakhali",
    "divisionKey": "chattogram",
    "nameBn": "নোয়াখালী",
    "nameEn": "Noakhali",
    "established": "1822",
    "areaKm2": 3686,
    "population": 3625252,
    "density": 984,
    "parliamentSeats": 6,
    "upazilaCount": 9,
    "govUrl": "https://noakhali.gov.bd",
    "famousBn": "নিঝুম দ্বীপ জাতীয় উদ্যান, গান্ধী আশ্রম (সোনাইমুড়ী), নোবিপ্রবি (NSTU), ভাসানচর, মেঘনা মোহনা",
    "famousEn": "Nijhum Dwip National Park, Gandhi Ashram, NSTU, Bhasan Char",
    "historyBn": "১৮২১ সালে ভুলুয়া নামে প্রতিষ্ঠিত এবং ১৮৬৮ সালে নোয়াখালী নামকরণ হয়। উপকূলীয় চর ও নিঝুম দ্বীপের জন্য সুপরিচিত।",
    "historyEn": "Established as Bhulua in 1821 and renamed Noakhali in 1868. Famous for coastal islands and Nijhum Dwip.",
    "examPointsBn": [
      "নিঝুম দ্বীপ (হাতিয়া)—মায়াবী চিত্রা হরিণের অভয়ারণ্য ও জাতীয় উদ্যান",
      "মহাত্মা গান্ধী ১৯৪৬ সালে নোয়াখালী সফর করেন (গান্ধী আশ্রম, জয়াগ)",
      "বীরশ্রেষ্ঠ শহীদ মো. রুহুল আমিনের জন্মস্থান (সোনাইমুড়ী)",
      "নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (NSTU)"
    ],
    "examPointsEn": [
      "Nijhum Dwip (Hatiya) — spotted deer sanctuary & National Park",
      "Gandhi Ashram at Jayag (Mahatma Gandhi’s 1946 peace mission)",
      "Birthplace of Bir Sreshtho Mohammad Ruhul Amin",
      "Noakhali Science & Technology University (NSTU)"
    ]
  },
  "rangamati": {
    "geoKey": "rangamati",
    "divisionKey": "chattogram",
    "nameBn": "রাঙামাটি",
    "nameEn": "Rangamati",
    "established": "1860",
    "areaKm2": 6116,
    "population": 647587,
    "density": 106,
    "parliamentSeats": 1,
    "upazilaCount": 10,
    "govUrl": "https://rangamati.gov.bd",
    "famousBn": "কাপ্তাই হ্রদ, কর্ণফুলী জলবিদ্যুৎ কেন্দ্র, সাজেক ভ্যালি, ঝুলন্ত সেতু, বাংলাদেশের বৃহত্তম জেলা",
    "famousEn": "Kaptai Lake, Karnaphuli Hydroelectric Plant, Sajek Valley, Hanging Bridge, Largest District",
    "historyBn": "পার্বত্য চট্টগ্রামের কেন্দ্র রাঙামাটি ১৮৬০ সালে জেলা হিসেবে প্রতিষ্ঠিত হয়। এটি আয়তনে বাংলাদেশের বৃহত্তম জেলা।",
    "historyEn": "Heart of Chittagong Hill Tracts, established in 1860. Largest district in Bangladesh by area.",
    "examPointsBn": [
      "আয়তনে বাংলাদেশের বৃহত্তম জেলা (৬,১১৬ বর্গ কি.মি.)",
      "ভারত ও মিয়ানমার—উভয় দেশের সাথে সীমান্ত থাকা একমাত্র জেলা",
      "কাপ্তাই কৃত্রিম হ্রদ ও দেশের একমাত্র জলবিদ্যুৎ কেন্দ্র (কর্ণফুলী, ১৯৬২)",
      "সাজেক ভ্যালি ও চাকমা-মারমা-ত্রিপুরা ক্ষুদ্র নৃগোষ্ঠীর সাংস্কৃতিক কেন্দ্র"
    ],
    "examPointsEn": [
      "Largest district of Bangladesh by area (6,116 km²)",
      "Only district bordering both India and Myanmar",
      "Kaptai Lake & Bangladesh’s only hydroelectric power plant (1962)",
      "Sajek Valley & rich indigenous cultural heritage"
    ]
  },
  "bandarban": {
    "geoKey": "bandarban",
    "divisionKey": "chattogram",
    "nameBn": "বান্দরবান",
    "nameEn": "Bandarban",
    "established": "1981",
    "areaKm2": 4479,
    "population": 481109,
    "density": 107,
    "parliamentSeats": 1,
    "upazilaCount": 7,
    "govUrl": "https://bandarban.gov.bd",
    "famousBn": "তাজিংডং/সাকা হাফং, কেওক্রাডং, বগালেক, নীলাচল, নীলগিরি, নাফাখুম জলপ্রপাত, সাঙ্গু নদী",
    "famousEn": "Tazing Dong / Saka Haphong, Keokradong, Boga Lake, Nilgiri, Nafakhum Waterfall, Sangu River",
    "historyBn": "১৯৮১ সালে জেলা হিসেবে গঠিত বান্দরবান বাংলাদেশের সর্বনিম্ন জনবহুল ও সবচেয়ে পাহাড়ি বৈচিত্র্যময় জেলা।",
    "historyEn": "Established in 1981; least populous and most mountainous district of Bangladesh.",
    "examPointsBn": [
      "বাংলাদেশের সর্বনিম্ন জনসংখ্যার জেলা ও সর্বনিম্ন জনঘনত্ব",
      "বাংলাদেশের সর্বোচ্চ পর্বতশৃঙ্গ (তাজিংডং/সাকা হাফং) ও কেওক্রাডং অবস্থিত",
      "বগালেক (প্রাকৃতিক পাহাড়ি হ্রদ), নাফাখুম ও আমিয়াখুম জলপ্রপাত",
      "সাঙ্গু নদী—উৎপত্তি ও সমাপ্তি দুটিই বাংলাদেশের ভেতরে"
    ],
    "examPointsEn": [
      "Least populous district & lowest population density in Bangladesh",
      "Home to Bangladesh’s highest peaks (Tazing Dong / Saka Haphong, Keokradong)",
      "Boga Lake (natural hill lake) & Nafakhum waterfall",
      "Sangu River — originates and ends entirely within Bangladesh"
    ]
  },
  "khagrachhari": {
    "geoKey": "khagrachhari",
    "divisionKey": "chattogram",
    "nameBn": "খাগড়াছড়ি",
    "nameEn": "Khagrachhari",
    "established": "1983",
    "areaKm2": 2749,
    "population": 714119,
    "density": 260,
    "parliamentSeats": 1,
    "upazilaCount": 9,
    "govUrl": "https://khagrachhari.gov.bd",
    "famousBn": "আলুটিলা প্রাকৃতিক গুহা, রিসাং ঝর্ণা, চেঙ্গী নদী, রামগড় স্থলবন্দর",
    "famousEn": "Alutila Natural Cave, Risang Waterfall, Chengi River, Ramgarh Land Port",
    "historyBn": "১৯৮৩ সালে গঠিত খাগড়াছড়ি জেলা পার্বত্য চট্টগ্রামের উত্তর অংশে অবস্থিত।",
    "historyEn": "Established in 1983 in the northern Chittagong Hill Tracts.",
    "examPointsBn": [
      "আলুটিলা প্রাকৃতিক সুড়ঙ্গ গুহা ও রিসাং ঝর্ণা",
      "রামগড় স্থলবন্দর (ভারতের ত্রিপুরার সাথে মৈত্রী সেতু-১ সংযুক্ত)",
      "চেঙ্গী ও মাইনী নদী উপত্যকা"
    ],
    "examPointsEn": [
      "Alutila natural cave & Risang waterfall",
      "Ramgarh Land Port connected via Maitri Setu-1 to Tripura, India",
      "Chengi & Maini river valleys"
    ]
  },
  "brahmanbaria": {
    "geoKey": "brahmanbaria",
    "divisionKey": "chattogram",
    "nameBn": "ব্রাহ্মণবাড়িয়া",
    "nameEn": "Brahmanbaria",
    "established": "1984",
    "areaKm2": 1881,
    "population": 3306559,
    "density": 1758,
    "parliamentSeats": 6,
    "upazilaCount": 9,
    "govUrl": "https://brahmanbaria.gov.bd",
    "famousBn": "তিতাস গ্যাসক্ষেত্র, আশুগঞ্জ বিদ্যুৎ ও নদীবন্দর, আখাউড়া স্থলবন্দর, ছানামুখী মিষ্টি (GI), ওস্তাদ আলাউদ্দিন খাঁ",
    "famousEn": "Titas Gas Field, Ashuganj Power & Port, Akhaura Land Port, Chhanamukhi (GI), Ustad Alauddin Khan",
    "historyBn": "১৯৮৪ সালে কুমিল্লা থেকে পৃথক হয়ে জেলা হয়। তিতাস গ্যাসক্ষেত্র ও উচ্চাঙ্গ সংগীতের জন্য \"সংস্কৃতির রাজধানী\" হিসেবে খ্যাত।",
    "historyEn": "Carved from Cumilla in 1984. Renowned for Titas Gas Field and classical music heritage.",
    "examPointsBn": [
      "তিতাস গ্যাসক্ষেত্র (১৯৬২)—বাংলাদেশের অন্যতম বৃহৎ প্রাকৃতিক গ্যাসক্ষেত্র",
      "ওস্তাদ আলাউদ্দিন খাঁ (উপমহাদেশের বিখ্যাত সরোদ বাদক)-এর জন্মস্থান",
      "আখাউড়া স্থলবন্দর ও আশুগঞ্জ অভ্যন্তরীণ নদীবন্দর",
      "ব্রাহ্মণবাড়িয়ার ছানামুখী (GI পণ্য) ও তিতাস নদী"
    ],
    "examPointsEn": [
      "Titas Gas Field (1962) — one of Bangladesh’s largest gas fields",
      "Birthplace of legendary sarod maestro Ustad Alauddin Khan",
      "Akhaura Land Port & Ashuganj river port/power hub",
      "Chhanamukhi sweet (GI product) & Titas River"
    ]
  },
  "coxs-bazar": {
    "geoKey": "coxs-bazar",
    "divisionKey": "chattogram",
    "nameBn": "কক্সবাজার",
    "nameEn": "Cox’s Bazar",
    "established": "1984",
    "areaKm2": 2492,
    "population": 2823265,
    "density": 1133,
    "parliamentSeats": 4,
    "upazilaCount": 9,
    "govUrl": "https://coxsbazar.gov.bd",
    "famousBn": "পৃথিবীর দীর্ঘতম অবিচ্ছিন্ন সমুদ্রসৈকত (১২০ কি.মি.), সেন্ট মার্টিন দ্বীপ, মহেশখালী, মাতারবাড়ী গভীর সমুদ্রবন্দর",
    "famousEn": "World’s longest unbroken sea beach (120 km), St. Martin’s Island, Moheshkhali, Matarbari Port",
    "historyBn": "ক্যাপ্টেন হিরাম কক্সের নামে নামকরণকৃত কক্সবাজার ১৯৮৪ সালে জেলা হয়। এটি বাংলাদেশের পর্যটন রাজধানী।",
    "historyEn": "Named after Captain Hiram Cox; established as a district in 1984. Tourism capital of Bangladesh.",
    "examPointsBn": [
      "বিশ্বের দীর্ঘতম প্রাকৃতিক সমুদ্রসৈকত (১২০ কি.মি.)",
      "সেন্ট মার্টিন (নারিকেল জিঞ্জিরা)—বাংলাদেশের একমাত্র প্রবাল দ্বীপ ও দক্ষিণতম স্থান (ছেঁড়া দ্বীপ)",
      "মহেশখালী—বাংলাদেশের একমাত্র পাহাড়ি দ্বীপ ও মাতারবাড়ী গভীর সমুদ্রবন্দর",
      "নাফ নদী বাংলাদেশ ও মিয়ানমারকে বিভক্ত করেছে"
    ],
    "examPointsEn": [
      "World’s longest natural unbroken sea beach (120 km)",
      "St. Martin’s Island — Bangladesh’s only coral island & Chhera Dwip southernmost point",
      "Moheshkhali — only hilly island & Matarbari Deep Sea Port",
      "Naf River forms the Bangladesh-Myanmar border"
    ]
  },
  "chandpur": {
    "geoKey": "chandpur",
    "divisionKey": "chattogram",
    "nameBn": "চাঁদপুর",
    "nameEn": "Chandpur",
    "established": "1984",
    "areaKm2": 1645,
    "population": 2635748,
    "density": 1602,
    "parliamentSeats": 5,
    "upazilaCount": 8,
    "govUrl": "https://chandpur.gov.bd",
    "famousBn": "ইলিশের বাড়ি (চাঁদপুরের ইলিশ GI), পদ্মা-মেঘনা-ডাকাতিয়ার মিলনস্থল (বড় স্টেশন মোলহেড), মৎস্য গবেষণা ইনস্টিটিউট",
    "famousEn": "Home of Hilsa (GI), Padma-Meghna-Dakatia confluence, Fisheries Research Institute",
    "historyBn": "পদ্মা, মেঘনা ও ডাকাতিয়া নদীর ত্রিমোহনায় অবস্থিত চাঁদপুর ১৯৮৪ সালে জেলা হিসেবে প্রতিষ্ঠিত হয়।",
    "historyEn": "Located at the confluence of Padma, Meghna, and Dakatia rivers; established as a district in 1984.",
    "examPointsBn": [
      "চাঁদপুরে পদ্মা ও মেঘনা নদীর মিলনস্থল (বড় স্টেশন মোলহেড)",
      "বাংলাদেশের ইলিশ (২য় GI পণ্য, ২০১৭) ও “ইলিশের বাড়ি চাঁদপুর”",
      "বাংলাদেশ মৎস্য গবেষণা ইনস্টিটিউট (নদী কেন্দ্র) অবস্থিত"
    ],
    "examPointsEn": [
      "Confluence of Padma, Meghna & Dakatia rivers",
      "Hilsa Fish (Bangladesh’s 2nd GI product, 2017) & \"City of Hilsa\"",
      "Riverine Station of Bangladesh Fisheries Research Institute"
    ]
  },
  "lakshmipur": {
    "geoKey": "lakshmipur",
    "divisionKey": "chattogram",
    "nameBn": "লক্ষ্মীপুর",
    "nameEn": "Lakshmipur",
    "established": "1984",
    "areaKm2": 1440,
    "population": 1938111,
    "density": 1346,
    "parliamentSeats": 4,
    "upazilaCount": 5,
    "govUrl": "https://lakshmipur.gov.bd",
    "famousBn": "নারিকেল ও সুপারি উৎপাদন, দালাল বাজার জমিদার বাড়ি, মতিরহাট মেঘনা তীর, সয়াবিন চাষ",
    "famousEn": "Coconut & Betel nut hub, Dalal Bazar Zamindar Bari, Soybean production",
    "historyBn": "১৯৮৪ সালে নোয়াখালী থেকে আলাদা হয়ে লক্ষ্মীপুর জেলা প্রতিষ্ঠিত হয়।",
    "historyEn": "Separated from Noakhali to become a district in 1984.",
    "examPointsBn": [
      "বাংলাদেশের সর্বাধিক সয়াবিন ও সুপারি উৎপাদনকারী অঞ্চলের একটি",
      "মেঘনা নদীর তীরে অবস্থিত উপকূলীয় কৃষিপ্রধান জেলা",
      "দালাল বাজার জমিদার বাড়ি ও খোয়া সাগর দিঘি"
    ],
    "examPointsEn": [
      "Leading soybean, coconut & betel nut producing coastal district",
      "Located along the Lower Meghna estuary",
      "Dalal Bazar Zamindar Bari historic site"
    ]
  },
  "feni": {
    "geoKey": "feni",
    "divisionKey": "chattogram",
    "nameBn": "ফেনী",
    "nameEn": "Feni",
    "established": "1984",
    "areaKm2": 990,
    "population": 1648896,
    "density": 1665,
    "parliamentSeats": 3,
    "upazilaCount": 6,
    "govUrl": "https://feni.gov.bd",
    "famousBn": "মুহুরী প্রজেক্ট (বাংলাদেশের প্রথম বায়ুবিদ্যুৎ কেন্দ্র), শমসের গাজীর দিঘি, বিলোনিয়া স্থলবন্দর",
    "famousEn": "Muhuri Project (1st wind power plant), Shamsher Gazi Dighi, Belonia Land Port",
    "historyBn": "১৯৮৪ সালে নোয়াখালী থেকে পৃথক হয়ে ফেনী জেলা গঠিত হয়। ঢাকা-চট্টগ্রাম মহাসড়কের কৌশলগত প্রবেশদ্বার।",
    "historyEn": "Formed from Noakhali in 1984; strategic corridor on the Dhaka-Chattogram highway.",
    "examPointsBn": [
      "সোনাগাজীতে মুহুরী প্রজেক্টে বাংলাদেশের প্রথম বায়ুবিদ্যুৎ কেন্দ্র স্থাপিত",
      "ফেনী নদী বাংলাদেশ-ভারত (ত্রিপুরা) সীমান্তের অংশ",
      "সাহিত্যিক শহীদুল্লা কায়সার ও চলচ্চিত্রকার জহির রায়হানের জন্মস্থান"
    ],
    "examPointsEn": [
      "Site of Bangladesh’s first wind power plant at Muhuri Project, Sonagazi",
      "Feni River forms part of the Bangladesh-India border",
      "Birthplace of Shahidullah Kaiser & Zahir Raihan"
    ]
  },
  "rajshahi": {
    "geoKey": "rajshahi",
    "divisionKey": "rajshahi",
    "nameBn": "রাজশাহী",
    "nameEn": "Rajshahi",
    "established": "1772",
    "areaKm2": 2425,
    "population": 2915013,
    "density": 1202,
    "parliamentSeats": 6,
    "upazilaCount": 9,
    "govUrl": "https://rajshahi.gov.bd",
    "famousBn": "সিল্ক সিটি (রাজশাহী সিল্ক GI), ফজলি আম, রাজশাহী বিশ্ববিদ্যালয় (১৯৫৩), বরেন্দ্র গবেষণা জাদুঘর, পুঠিয়া রাজবাড়ী",
    "famousEn": "Silk City (Rajshahi Silk GI), Fazli Mango, Rajshahi University (1953), Varendra Museum, Puthia",
    "historyBn": "১৭৭২ সালে প্রতিষ্ঠিত রাজশাহী প্রাচীন বরেন্দ্র ভূমির অন্যতম প্রধান শহর। শিক্ষা ও রেশমশিল্পের জন্য \"সিল্ক সিটি\" নামে খ্যাত।",
    "historyEn": "Established in 1772 in ancient Varendra. Known as \"Silk City\" and \"Education City\".",
    "examPointsBn": [
      "বরেন্দ্র গবেষণা জাদুঘর (১৯১০)—বাংলাদেশের প্রথম ও প্রাচীনতম জাদুঘর",
      "রাজশাহী বিশ্ববিদ্যালয় (১৯৫৩), রুয়েট (RUET) ও সারদা পুলিশ একাডেমি",
      "রাজশাহী সিল্ক ও রাজশাহী-চাঁপাইনবাবগঞ্জের ফজলি আম (GI পণ্য)",
      "বাঘা মসজিদ (৫০ টাকার নোটে ছিল) ও পুঠিয়া মন্দির কমপ্লেক্স"
    ],
    "examPointsEn": [
      "Varendra Research Museum (1910) — oldest museum in Bangladesh",
      "University of Rajshahi (1953), RUET & Sardah Police Academy",
      "Rajshahi Silk & Fazli Mango (GI products)",
      "Bagha Mosque & Puthia Temple Complex"
    ]
  },
  "bogura": {
    "geoKey": "bogura",
    "divisionKey": "rajshahi",
    "nameBn": "বগুড়া",
    "nameEn": "Bogura",
    "established": "1821",
    "areaKm2": 2899,
    "population": 3734300,
    "density": 1288,
    "parliamentSeats": 7,
    "upazilaCount": 13,
    "govUrl": "https://bogra.gov.bd",
    "famousBn": "মহাস্থানগড় (পুণ্ড্রনগর), বগুড়ার দই (GI পণ্য), উত্তরবঙ্গের প্রবেশদ্বার, করতোয়া নদী",
    "famousEn": "Mahasthangarh (Pundranagara), Bogura Doi (GI), Gateway to North Bengal, Karatoya River",
    "historyBn": "১৮২১ সালে প্রতিষ্ঠিত বগুড়া জেলায় প্রাচীন বাংলার সবচেয়ে পুরনো নগরী পুণ্ড্রবর্ধনের রাজধানী মহাস্থানগড় অবস্থিত।",
    "historyEn": "Established in 1821; hosts Mahasthangarh, capital of ancient Pundravardhana.",
    "examPointsBn": [
      "মহাস্থানগড়—বাংলাদেশের প্রাচীনতম ঐতিহাসিক নগরী (খ্রিস্টপূর্ব ৩য় শতক, করতোয়া তীরে)",
      "বগুড়ার দই (বাংলাদেশের স্বীকৃত GI পণ্য)",
      "উত্তরবঙ্গের বাণিজ্যিক প্রবেশদ্বার ও পল্লী উন্নয়ন একাডেমি (RDA)"
    ],
    "examPointsEn": [
      "Mahasthangarh — oldest urban archaeological site (~3rd century BCE on Karatoya)",
      "Bogura Doi (recognized GI product)",
      "Commercial gateway to North Bengal & Rural Development Academy (RDA)"
    ]
  },
  "pabna": {
    "geoKey": "pabna",
    "divisionKey": "rajshahi",
    "nameBn": "পাবনা",
    "nameEn": "Pabna",
    "established": "1832",
    "areaKm2": 2376,
    "population": 2909622,
    "density": 1225,
    "parliamentSeats": 5,
    "upazilaCount": 9,
    "govUrl": "https://pabna.gov.bd",
    "famousBn": "রূপপুর পারমাণবিক বিদ্যুৎকেন্দ্র, হার্ডিঞ্জ ব্রিজ, লালন শাহ সেতু, পাবনা মানসিক হাসপাতাল, ঈশ্বরদী EPZ",
    "famousEn": "Rooppur Nuclear Power Plant, Hardinge Bridge, Lalon Shah Bridge, Pabna Mental Hospital",
    "historyBn": "১৮২৮ সালে প্রতিষ্ঠিত পাবনা জেলা বাংলাদেশের প্রথম পারমাণবিক বিদ্যুৎকেন্দ্র ও ঐতিহাসিক হার্ডিঞ্জ ব্রিজের জন্য বিখ্যাত।",
    "historyEn": "Established in 1828; hosts Bangladesh’s first nuclear power plant and historic Hardinge Bridge.",
    "examPointsBn": [
      "রূপপুর পারমাণবিক বিদ্যুৎকেন্দ্র (ঈশ্বরদী—২৪০০ মেগাওয়াট, রাশিয়ার সহায়তায়)",
      "হার্ডিঞ্জ ব্রিজ (১৯১৫ সালে পদ্মা নদীর ওপর নির্মিত ঐতিহাসিক রেলসেতু)",
      "বাংলাদেশের একমাত্র বিশেষায়িত মানসিক হাসপাতাল (হেমায়েতপুর, ১৯৫৭)",
      "পাবনা বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (PUST) ও ঈশ্বরদী EPZ"
    ],
    "examPointsEn": [
      "Rooppur Nuclear Power Plant (Ishwardi — 2,400 MW)",
      "Hardinge Bridge (1915 historic railway bridge over Padma)",
      "Bangladesh’s specialized Mental Hospital (1957)",
      "PUST university & Ishwardi EPZ"
    ]
  },
  "joypurhat": {
    "geoKey": "joypurhat",
    "divisionKey": "rajshahi",
    "nameBn": "জয়পুরহাট",
    "nameEn": "Joypurhat",
    "established": "1984",
    "areaKm2": 1012,
    "population": 956430,
    "density": 945,
    "parliamentSeats": 6,
    "upazilaCount": 5,
    "govUrl": "https://joypurhat.gov.bd",
    "famousBn": "জামালগঞ্জ কয়লাখনি, চুনাপাথর খনি, বারো শিবালয় মন্দির, আলু ও কৃষি উৎপাদন",
    "famousEn": "Jamalganj Coal Mine, Limestone reserves, Baro Shibaloy Temple",
    "historyBn": "১৯৮৪ সালে বগুড়া থেকে আলাদা হয়ে জয়পুরহাট জেলা প্রতিষ্ঠিত হয়। খনিজ সম্পদ ও কৃষির জন্য গুরুত্বপূর্ণ।",
    "historyEn": "Carved from Bogura in 1984; known for mineral reserves (coal, limestone) and agriculture.",
    "examPointsBn": [
      "জামালগঞ্জ কয়লাখনি—বাংলাদেশের অন্যতম বৃহৎ কয়লা মজুদ",
      "জয়পুরহাটে দেশের অন্যতম চুনাপাথর খনি অবস্থিত",
      "তুলসীগঙ্গা ও ছোট যমুনা নদী প্রবাহিত"
    ],
    "examPointsEn": [
      "Jamalganj Coal Mine — major coal reserve in Bangladesh",
      "Important limestone mineral deposits",
      "Traversed by Tulsiganga & Little Jamuna rivers"
    ]
  },
  "sirajganj": {
    "geoKey": "sirajganj",
    "divisionKey": "rajshahi",
    "nameBn": "সিরাজগঞ্জ",
    "nameEn": "Sirajganj",
    "established": "1984",
    "areaKm2": 2402,
    "population": 3357708,
    "density": 1398,
    "parliamentSeats": 6,
    "upazilaCount": 9,
    "govUrl": "https://sirajganj.gov.bd",
    "famousBn": "যমুনা বহুমুখী সেতুর পশ্চিম প্রান্ত, শাহজাদপুর রবীন্দ্র কুঠিবাড়ি, তাঁতশিল্প, বাঘাবাড়ী দুগ্ধ ও নদীবন্দর",
    "famousEn": "Jamuna Bridge west end, Shahzadpur Rabindra Kuthibari, Handloom, Baghabari Port",
    "historyBn": "১৯৮৪ সালে পাবনা থেকে পৃথক হয়ে সিরাজগঞ্জ জেলা হয়। যমুনা তীরবর্তী তাঁতশিল্প ও শাহজাদপুরের রবীন্দ্র স্মৃতির জন্য বিখ্যাত।",
    "historyEn": "Formed from Pabna in 1984; famous for handloom weaving and Rabindranath Tagore’s Shahzadpur Kuthibari.",
    "examPointsBn": [
      "যমুনা বহুমুখী সেতু ও বঙ্গবন্ধু শেখ মুজিব রেলওয়ে সেতুর পশ্চিম প্রান্ত",
      "শাহজাদপুর রবীন্দ্র কুঠিবাড়ি ও রবীন্দ্র বিশ্ববিদ্যালয়",
      "বাঘাবাড়ী নদীবন্দর ও মিল্ক ভিটা দুগ্ধশিল্প অঞ্চল"
    ],
    "examPointsEn": [
      "Western end of Jamuna Bridge & Bangabandhu Railway Bridge",
      "Shahzadpur Rabindra Kuthibari & Rabindra University",
      "Baghabari river port & Milk Vita dairy belt"
    ]
  },
  "naogaon": {
    "geoKey": "naogaon",
    "divisionKey": "rajshahi",
    "nameBn": "নওগাঁ",
    "nameEn": "Naogaon",
    "established": "1984",
    "areaKm2": 3436,
    "population": 2784598,
    "density": 811,
    "parliamentSeats": 4,
    "upazilaCount": 11,
    "govUrl": "https://naogaon.gov.bd",
    "famousBn": "পাহাড়পুর বৌদ্ধ বিহার (UNESCO), কুসুম্বা মসজিদ, রবীন্দ্রনাথের পতিসর কুঠিবাড়ি, নাকফজলি আম, ধান উৎপাদন",
    "famousEn": "Somapura Mahavihara Paharpur (UNESCO), Kusumba Mosque, Patisar Kuthibari, Rice bowl",
    "historyBn": "১৯৮৪ সালে জেলা হয়। পাল আমলের বিশ্বখ্যাত সোমপুর মহাবিহার (পাহাড়পুর) এই জেলায় অবস্থিত।",
    "historyEn": "Established in 1984; home to UNESCO World Heritage Site Somapura Mahavihara (Paharpur).",
    "examPointsBn": [
      "পাহাড়পুর বৌদ্ধ বিহার (সোমপুর মহাবিহার, ধর্মপাল নির্মিত—১৯৮৫ সালে UNESCO বিশ্ব ঐতিহ্য)",
      "কুসুম্বা মসজিদ (৫ টাকার নোটে ব্যবহৃত, মান্দা)",
      "রবীন্দ্রনাথ ঠাকুরের পতিসর কুঠিবাড়ি (আত্রাই) ও আলতাদিঘী জাতীয় উদ্যান",
      "বাংলাদেশের অন্যতম শীর্ষ ধান উৎপাদনকারী জেলা"
    ],
    "examPointsEn": [
      "Paharpur Buddhist Vihara (built by Dharmapala, UNESCO Heritage 1985)",
      "Kusumba Mosque (featured on 5 Taka note)",
      "Rabindranath Tagore’s Patisar Kuthibari & Altadighi National Park",
      "One of Bangladesh’s top rice-producing districts"
    ]
  },
  "natore": {
    "geoKey": "natore",
    "divisionKey": "rajshahi",
    "nameBn": "নাটোর",
    "nameEn": "Natore",
    "established": "1984",
    "areaKm2": 1900,
    "population": 1859921,
    "density": 979,
    "parliamentSeats": 3,
    "upazilaCount": 7,
    "govUrl": "https://natore.gov.bd",
    "famousBn": "উত্তরা গণভবন (দিঘাপতিয়া রাজবাড়ী), কাঁচাগোল্লা (GI পণ্য), চলনবিল, রানী ভবানীর রাজবাড়ী",
    "famousEn": "Uttara Ganabhaban, Kachagolla (GI), Chalan Beel, Rani Bhabani Palace",
    "historyBn": "রানী ভবানীর স্মৃতিবিজড়িত নাটোর ১৯৮৪ সালে জেলা হিসেবে প্রতিষ্ঠিত হয়।",
    "historyEn": "Historic seat of Rani Bhabani; became a district in 1984.",
    "examPointsBn": [
      "উত্তরা গণভবন (দিঘাপতিয়া রাজবাড়ী)—বাংলাদেশের প্রধানমন্ত্রীর উত্তরাঞ্চলীয় বাসভবন",
      "নাটোরের কাঁচাগোল্লা (স্বীকৃত GI পণ্য)",
      "চলনবিল—বাংলাদেশের বৃহত্তম বিল (নাটোর, সিরাজগঞ্জ ও পাবনা বিস্তৃত)",
      "বাংলাদেশের সর্বনিম্ন বার্ষিক বৃষ্টিপাত হয় লালপুর উপজেলায়"
    ],
    "examPointsEn": [
      "Uttara Ganabhaban (Dighapatia Palace)",
      "Natore Kachagolla (GI product)",
      "Chalan Beel — largest wetland/beel in Bangladesh",
      "Lalpur upazila records Bangladesh’s lowest annual rainfall"
    ]
  },
  "chapainawabganj": {
    "geoKey": "chapainawabganj",
    "divisionKey": "rajshahi",
    "nameBn": "চাঁপাইনবাবগঞ্জ",
    "nameEn": "Chapainawabganj",
    "established": "1984",
    "areaKm2": 1703,
    "population": 1835527,
    "density": 1078,
    "parliamentSeats": 2,
    "upazilaCount": 5,
    "govUrl": "https://chapainawabganj.gov.bd",
    "famousBn": "আমের রাজধানী (খিরসাপাত/ল্যাংড়া/আশ্বিনা GI), ছোট সোনা মসজিদ, সোনামসজিদ স্থলবন্দর, পশ্চিমতম স্থান (শিবগঞ্জ)",
    "famousEn": "Mango Capital (Khirshapat/Langra GI), Choto Sona Mosque, Westernmost point (Shibganj)",
    "historyBn": "১৯৮৪ সালে রাজশাহী থেকে পৃথক জেলা হয়। বাংলার সুলতানি আমলের গৌড় রাজ্যের গুরুত্বপূর্ণ নিদর্শন এখানে অবস্থিত।",
    "historyEn": "Formed from Rajshahi in 1984; rich in Sultanate-era Gaur heritage and famed as Bangladesh’s mango capital.",
    "examPointsBn": [
      "বাংলাদেশের সর্বপশ্চিমের উপজেলা (শিবগঞ্জ) ও স্থান (মনাকষা)",
      "ছোট সোনা মসজিদ (আলাউদ্দিন হোসেন শাহের আমলে নির্মিত, সুলতানি স্থাপত্য)",
      "ক্ষীরসাপাত আম (৩য় GI পণ্য), ল্যাংড়া ও আশ্বিনা আম",
      "বীরশ্রেষ্ঠ ক্যাপ্টেন মহিউদ্দীন জাহাঙ্গীরের সমাধি (ছোট সোনা মসজিদ প্রাঙ্গণ)"
    ],
    "examPointsEn": [
      "Westernmost upazila (Shibganj) & point (Manakasha) of Bangladesh",
      "Choto Sona Mosque (Sultanate-era architectural gem)",
      "Khirshapat (3rd GI), Langra & Ashwina mangoes",
      "Mausoleum of Bir Sreshtho Capt. Mohiuddin Jahangir"
    ]
  },
  "jashore": {
    "geoKey": "jashore",
    "divisionKey": "khulna",
    "nameBn": "যশোর",
    "nameEn": "Jashore",
    "established": "1781",
    "areaKm2": 2607,
    "population": 3076849,
    "density": 1181,
    "parliamentSeats": 6,
    "upazilaCount": 8,
    "govUrl": "https://jashore.gov.bd",
    "famousBn": "দেশের প্রথম ডিজিটাল জেলা, বেনাপোল স্থলবন্দর, গদখালী ফুলের বাজার, মাইকেল মধুসূদন দত্ত (সাগরদাঁড়ি), খেজুর গুড়",
    "famousEn": "First Digital District, Benapole Land Port, Gadkhali Flower Hub, Michael Madhusudan Dutt",
    "historyBn": "১৭৮১ সালে প্রতিষ্ঠিত যশোর বাংলাদেশের প্রাচীনতম জেলাগুলোর একটি এবং ৬ ডিসেম্বর ১৯৭১ প্রথম শত্রুমুক্ত জেলা।",
    "historyEn": "Established in 1781; one of Bangladesh’s oldest districts and the first district liberated on 6 Dec 1971.",
    "examPointsBn": [
      "১৯৭১ সালের ৬ ডিসেম্বর বাংলাদেশের প্রথম শত্রুমুক্ত জেলা",
      "বেনাপোল স্থলবন্দর—বাংলাদেশের বৃহত্তম স্থলবন্দর",
      "মহাকবি মাইকেল মধুসূদন দত্তের জন্মস্থান (সাগরদাঁড়ি, কেশবপুর, কপোতাক্ষ নদ)",
      "গদখালী ফুলের রাজধানী, জবিপ্রবি (JUST) ও শেখ হাসিনা সফটওয়্যার টেকনোলজি পার্ক"
    ],
    "examPointsEn": [
      "First liberated district of Bangladesh (6 December 1971)",
      "Benapole Land Port — largest land port of Bangladesh",
      "Birthplace of poet Michael Madhusudan Dutt (Sagardari on Kopotaksho)",
      "Gadkhali flower market, JUST university & Software Tech Park"
    ]
  },
  "khulna": {
    "geoKey": "khulna",
    "divisionKey": "khulna",
    "nameBn": "খুলনা",
    "nameEn": "Khulna",
    "established": "1882",
    "areaKm2": 4394,
    "population": 2613385,
    "density": 595,
    "parliamentSeats": 6,
    "upazilaCount": 9,
    "govUrl": "https://khulna.gov.bd",
    "famousBn": "সুন্দরবন (UNESCO), রূপসা সেতু, খুলনা বিশ্ববিদ্যালয়, কুয়েট (KUET), খুলনা শিপইয়ার্ড, চুইঝাল",
    "famousEn": "Sundarbans (UNESCO), Rupsha Bridge, Khulna University, KUET, Khulna Shipyard, Chuijhal",
    "historyBn": "১৮৮২ সালে প্রতিষ্ঠিত খুলনা দক্ষিণ-পশ্চিমাঞ্চলের প্রধান বন্দর-শিল্পনগরী ও সুন্দরবনের প্রবেশদ্বার।",
    "historyEn": "Established in 1882; industrial hub of southwestern Bangladesh and gateway to the Sundarbans.",
    "examPointsBn": [
      "সুন্দরবন (বিশ্বের বৃহত্তম ম্যানগ্রোভ বন—১৯৯৭ সালে UNESCO বিশ্ব ঐতিহ্য)",
      "খুলনা বিশ্ববিদ্যালয় (১৯৯১) ও কুয়েট (KUET)",
      "রূপসা, ভৈরব ও পশুর নদী এবং খুলনা শিপইয়ার্ড",
      "রবীন্দ্রনাথের শ্বশুরবাড়ি (দক্ষিণডিহি, ফুলতলা) ও চুকনগর বধ্যভূমি"
    ],
    "examPointsEn": [
      "Sundarbans — world’s largest mangrove forest (UNESCO Heritage 1997)",
      "Khulna University (1991) & KUET",
      "Rupsha, Bhairab & Pasur rivers + Khulna Shipyard",
      "Dakshin Dihi (Tagore’s in-laws’ home) & Chuknagar Memorial"
    ]
  },
  "kushtia": {
    "geoKey": "kushtia",
    "divisionKey": "khulna",
    "nameBn": "কুষ্টিয়া",
    "nameEn": "Kushtia",
    "established": "1947",
    "areaKm2": 1609,
    "population": 2149692,
    "density": 1336,
    "parliamentSeats": 4,
    "upazilaCount": 6,
    "govUrl": "https://kushtia.gov.bd",
    "famousBn": "লালন শাহের আখড়া (ছেঁউড়িয়া), শিলাইদহ রবীন্দ্র কুঠিবাড়ি, ইসলামী বিশ্ববিদ্যালয় (IU), তিলের খাজা (GI)",
    "famousEn": "Lalon Shah Shrine (Cheuria), Shilaidaha Kuthibari, Islamic University (IU), Tiler Khaja (GI)",
    "historyBn": "১৯৪৭ সালে নদীয়া থেকে বিভক্ত হয়ে কুষ্টিয়া জেলা গঠিত হয়। বাউল সম্রাট লালন ও রবীন্দ্রস্মৃতির জন্য \"সাংস্কৃতিক রাজধানী\" নামে পরিচিত।",
    "historyEn": "Formed in 1947 from Nadia; celebrated as the \"Cultural Capital\" for Lalon Shah and Tagore’s Shilaidaha.",
    "examPointsBn": [
      "শিলাইদহ রবীন্দ্র কুঠিবাড়ি—রবীন্দ্রনাথ এখানে ‘গীতাঞ্জলি’ ও ‘সোনার তরী’ রচনা করেন",
      "মরমী সাধক লালন সাঁইয়ের আখড়া (ছেঁউড়িয়া)",
      "ইসলামী বিশ্ববিদ্যালয় (১৯৭৯)—স্বাধীন বাংলাদেশে প্রতিষ্ঠিত প্রথম বিশ্ববিদ্যালয়",
      "কুষ্টিয়ার তিলের খাজা (GI পণ্য) ও গড়াই নদী"
    ],
    "examPointsEn": [
      "Shilaidaha Kuthibari where Tagore wrote Gitanjali & Sonar Tari",
      "Shrine of mystic Baul saint Lalon Shah at Cheuria",
      "Islamic University (1979) — first university founded after independence",
      "Kushtia Tiler Khaja (GI product) & Gorai River"
    ]
  },
  "satkhira": {
    "geoKey": "satkhira",
    "divisionKey": "khulna",
    "nameBn": "সাতক্ষীরা",
    "nameEn": "Satkhira",
    "established": "1984",
    "areaKm2": 3817,
    "population": 2196581,
    "density": 574,
    "parliamentSeats": 4,
    "upazilaCount": 7,
    "govUrl": "https://satkhira.gov.bd",
    "famousBn": "সুন্দরবনের পশ্চিম অংশ, ভোমরা স্থলবন্দর, সাতক্ষীরার হিমসাগর আম, চিংড়ি (সাদা সোনা), কালীগঞ্জ",
    "famousEn": "Western Sundarbans, Bhomra Land Port, Himsagar Mango, Shrimp (\"White Gold\")",
    "historyBn": "১৯৮৪ সালে খুলনা থেকে পৃথক হয়ে সাতক্ষীরা জেলা হয়। সুন্দরবন ও চিংড়ি রপ্তানির জন্য বিখ্যাত।",
    "historyEn": "Separated from Khulna in 1984; famous for Sundarbans ecotourism and shrimp exports.",
    "examPointsBn": [
      "ভোমরা স্থলবন্দর (ভারতের পশ্চিমবঙ্গের সাথে বাণিজ্য পথ)",
      "বাংলাদেশের চিংড়ি (সাদা সোনা) ও হিমসাগর আম উৎপাদনে শীর্ষ",
      "রায়মঙ্গল ও হাড়িয়াভাঙ্গা নদী (বাংলাদেশ-ভারত সীমান্ত নদী)"
    ],
    "examPointsEn": [
      "Bhomra Land Port connecting to West Bengal, India",
      "Major shrimp (\"White Gold\") & Himsagar mango producer",
      "Raimangal & Hariabhanga border rivers"
    ]
  },
  "jhenaidah": {
    "geoKey": "jhenaidah",
    "divisionKey": "khulna",
    "nameBn": "ঝিনাইদহ",
    "nameEn": "Jhenaidah",
    "established": "1984",
    "areaKm2": 1965,
    "population": 1771304,
    "density": 902,
    "parliamentSeats": 4,
    "upazilaCount": 6,
    "govUrl": "https://jhenaidah.gov.bd",
    "famousBn": "গাজী কালু চম্পাবতীর মাজার (বারোবাজার), মিয়ার দালান, এশিয়ার বৃহত্তম বটগাছ (মল্লিকপুর), কলা উৎপাদন",
    "famousEn": "Barobazar Historic Mosques, Mallikpur Giant Banyan Tree, Banana hub",
    "historyBn": "১৯৮৪ সালে যশোর থেকে আলাদা হয়ে ঝিনাইদহ জেলা গঠিত হয়।",
    "historyEn": "Carved from Jashore in 1984; noted for medieval Barobazar mosques and agriculture.",
    "examPointsBn": [
      "বীরশ্রেষ্ঠ সিপাহী হামিদুর রহমানের জন্মস্থান (মহেশপুর)",
      "বারোবাজার প্রত্নতাত্ত্বিক মসজিদ নগরী (সাতগাছিয়া, গোড়ার মসজিদ)",
      "মল্লিকপুরের ঐতিহাসিক বিশাল বটগাছ ও নবগঙ্গা নদী"
    ],
    "examPointsEn": [
      "Birthplace of Bir Sreshtho Sepoy Hamidur Rahman (Maheshpur)",
      "Barobazar medieval Sultanate mosque cluster",
      "Giant Banyan Tree of Mallikpur & Nabaganga River"
    ]
  },
  "bagerhat": {
    "geoKey": "bagerhat",
    "divisionKey": "khulna",
    "nameBn": "বাগেরহাট",
    "nameEn": "Bagerhat",
    "established": "1984",
    "areaKm2": 3959,
    "population": 1613079,
    "density": 407,
    "parliamentSeats": 4,
    "upazilaCount": 9,
    "govUrl": "https://bagerhat.gov.bd",
    "famousBn": "ষাট গম্বুজ মসজিদ (UNESCO), মোংলা সমুদ্রবন্দর, সুন্দরবন, হযরত খানজাহান আলী (র.)-এর মাজার",
    "famousEn": "Sixty Dome Mosque (UNESCO), Mongla Seaport, Sundarbans, Khan Jahan Ali Shrine",
    "historyBn": "পঞ্চদশ শতকে উলুঘ খান জাহান আলী (র.) খলিফাতাবাদ নগরী গড়ে তোলেন; ১৯৮৪ সালে বাগেরহাট জেলা হয়।",
    "historyEn": "Founded as Khalifatabad in the 15th century by Khan Jahan Ali; became a district in 1984.",
    "examPointsBn": [
      "ষাট গম্বুজ মসজিদ (৮১টি গম্বুজ, ৬০টি স্তম্ভ—১৯৮৫ সালে UNESCO বিশ্ব ঐতিহ্য)",
      "মোংলা সমুদ্রবন্দর—দেশের দ্বিতীয় বৃহত্তম সমুদ্রবন্দর (পশুর নদীর তীরে)",
      "রামপাল তাপবিদ্যুৎ কেন্দ্র (মৈত্রী সুপার থার্মাল)",
      "খানজাহান আলীর (র.) মাজার ও ঘোড়া দিঘি"
    ],
    "examPointsEn": [
      "Sixty Dome Mosque (81 domes, 60 pillars — UNESCO World Heritage 1985)",
      "Mongla Seaport — Bangladesh’s 2nd largest seaport on Pasur River",
      "Rampal Maitree Super Thermal Power Plant",
      "Historic Mosque City of Bagerhat & Khan Jahan Ali shrine"
    ]
  },
  "chuadanga": {
    "geoKey": "chuadanga",
    "divisionKey": "khulna",
    "nameBn": "চুয়াডাঙ্গা",
    "nameEn": "Chuadanga",
    "established": "1984",
    "areaKm2": 1174,
    "population": 1234066,
    "density": 1051,
    "parliamentSeats": 2,
    "upazilaCount": 4,
    "govUrl": "https://chuadanga.gov.bd",
    "famousBn": "দর্শনা রেল স্থলবন্দর, কেরু অ্যান্ড কোং (১৯৩৮), মাথাভাঙ্গা নদী, ব্ল্যাক বেঙ্গল ছাগল",
    "famousEn": "Darshana Rail Land Port, Carew & Co (1938), Mathabhanga River, Black Bengal Goat",
    "historyBn": "১৯৮৪ সালে কুষ্টিয়া থেকে পৃথক জেলা হয়। মুক্তিযুদ্ধের প্রথম অস্থায়ী রাজধানী হিসেবে চুয়াডাঙ্গা গুরুত্বপূর্ণ।",
    "historyEn": "Formed from Kushtia in 1984; key railway border gateway at Darshana.",
    "examPointsBn": [
      "দর্শনা স্থলবন্দর—বাংলাদেশের প্রথম রেলওয়ে সংযোগ (১৮৬২ সালে কলকাতা-দর্শনা-জগতী)",
      "কেরু অ্যান্ড কোম্পানি (১৯৩৮, দর্শনা চিনিকল)",
      "ব্ল্যাক বেঙ্গল ছাগলের (GI) প্রধান প্রজনন এলাকা ও সর্বোচ্চ গ্রীষ্মকালীন তাপমাত্রা অঞ্চল"
    ],
    "examPointsEn": [
      "Darshana — site of first railway line in Bengal (1862)",
      "Carew & Co. sugar mill & distillery (1938)",
      "Core Black Bengal Goat (GI) region & highest summer temperatures"
    ]
  },
  "magura": {
    "geoKey": "magura",
    "divisionKey": "khulna",
    "nameBn": "মাগুরা",
    "nameEn": "Magura",
    "established": "1984",
    "areaKm2": 1039,
    "population": 1033115,
    "density": 994,
    "parliamentSeats": 2,
    "upazilaCount": 4,
    "govUrl": "https://magura.gov.bd",
    "famousBn": "রাজা সীতারাম রায়ের প্রাসাদ (মহম্মদপুর), নবগঙ্গা ও মধুমতী নদী, ক্রিকেট তারকা সাকিব আল হাসান",
    "famousEn": "Raja Sitaram Roy Palace, Nabaganga & Madhumati Rivers",
    "historyBn": "১৯৮৪ সালে যশোর থেকে আলাদা হয়ে মাগুরা জেলা হিসেবে প্রতিষ্ঠিত হয়।",
    "historyEn": "Established as a district from Jashore in 1984.",
    "examPointsBn": [
      "মহম্মদপুরে রাজা সীতারাম রায়ের ঐতিহাসিক প্রাসাদ",
      "নবগঙ্গা, গড়াই ও মধুমতী নদী প্রবাহিত",
      "কবি ফররুখ আহমদের জন্মস্থান (মাঝাইল)"
    ],
    "examPointsEn": [
      "Raja Sitaram Roy’s historic palace at Mohammadpur",
      "Nabaganga, Gorai & Madhumati rivers",
      "Birthplace of poet Farrukh Ahmad"
    ]
  },
  "narail": {
    "geoKey": "narail",
    "divisionKey": "khulna",
    "nameBn": "নড়াইল",
    "nameEn": "Narail",
    "established": "1984",
    "areaKm2": 968,
    "population": 788673,
    "density": 815,
    "parliamentSeats": 2,
    "upazilaCount": 3,
    "govUrl": "https://narail.gov.bd",
    "famousBn": "শিল্পী এস এম সুলতানের স্মৃতিসংগ্রহশালা, বীরশ্রেষ্ঠ নূর মোহাম্মদ শেখ, চিত্রা নদী, নিরিবিলি পিকনিক স্পট",
    "famousEn": "Artist SM Sultan Complex, Bir Sreshtho Nur Mohammad Sheikh, Chitra River",
    "historyBn": "১৯৮৪ সালে যশোর থেকে পৃথক হয়ে নড়াইল জেলা হয়। চিত্রা নদীর তীরে অবস্থিত এই জেলা শিল্প-সংস্কৃতির জন্য খ্যাত।",
    "historyEn": "Formed from Jashore in 1984 along the scenic Chitra River.",
    "examPointsBn": [
      "বিশ্ববরেণ্য চিত্রশিল্পী এস এম সুলতানের জন্মস্থান ও স্মৃতিসংগ্রহশালা",
      "বীরশ্রেষ্ঠ ল্যান্স নায়েক নূর মোহাম্মদ শেখের জন্মস্থান (মহিষখোলা)",
      "চিত্রা ও মধুমতী নদী প্রবাহিত"
    ],
    "examPointsEn": [
      "Birthplace & Museum of renowned painter S.M. Sultan",
      "Birthplace of Bir Sreshtho Lance Naik Nur Mohammad Sheikh",
      "Traversed by Chitra & Madhumati rivers"
    ]
  },
  "meherpur": {
    "geoKey": "meherpur",
    "divisionKey": "khulna",
    "nameBn": "মেহেরপুর",
    "nameEn": "Meherpur",
    "established": "1984",
    "areaKm2": 742,
    "population": 705356,
    "density": 951,
    "parliamentSeats": 2,
    "upazilaCount": 3,
    "govUrl": "https://meherpur.gov.bd",
    "famousBn": "মুজিবনগর (বৈদ্যনাথতলা আম্রকানন), প্রথম অস্থায়ী সরকারের শপথ (১৭ এপ্রিল ১৯৭১), হিমসাগর আম",
    "famousEn": "Mujibnagar (Baidyanathtala), First Provisional Govt Oath (17 Apr 1971)",
    "historyBn": "১৯৮৪ সালে কুষ্টিয়া থেকে আলাদা হয়ে মেহেরপুর জেলা হয়। এখানেই বাংলাদেশের প্রথম সরকার শপথ নেয়।",
    "historyEn": "Separated from Kushtia in 1984; historic site of Bangladesh’s first provisional government oath.",
    "examPointsBn": [
      "১৯৭১ সালের ১৭ এপ্রিল মুজিবনগর (বৈদ্যনাথতলা) সরকার শপথ গ্রহণ করে",
      "বাংলাদেশের আয়তনে দ্বিতীয় ক্ষুদ্রতম জেলা (৭১৬ বর্গ কি.মি.)",
      "ভৈরব নদ ও মুজিবনগর স্মৃতিসৌধ (স্থপতি: তানভীর করিম)"
    ],
    "examPointsEn": [
      "Provisional Government of Bangladesh took oath here on 17 April 1971",
      "Second smallest district by area (716 km²)",
      "Mujibnagar Memorial Complex & Bhairab River"
    ]
  },
  "barishal": {
    "geoKey": "barishal",
    "divisionKey": "barishal",
    "nameBn": "বরিশাল",
    "nameEn": "Barishal",
    "established": "1797",
    "areaKm2": 2785,
    "population": 2570450,
    "density": 923,
    "parliamentSeats": 6,
    "upazilaCount": 10,
    "govUrl": "https://barisal.gov.bd",
    "famousBn": "বাংলার ভেনিস, ধান-নদী-খাল, দুর্গাসাগর দিঘি, গুঠিয়া মসজিদ, বরিশাল বিশ্ববিদ্যালয়, জীবনানন্দ দাশ",
    "famousEn": "Venice of Bengal, Durgasagar Dighi, Guthia Mosque, Barishal University, Jibanananda Das",
    "historyBn": "১৭৯৭ সালে বাকেরগঞ্জ জেলা হিসেবে প্রতিষ্ঠিত; পরে বরিশাল নামকরণ হয়। নদীমাতৃক সৌন্দর্যের জন্য \"বাংলার ভেনিস\" নামে পরিচিত।",
    "historyEn": "Established as Bakerganj district in 1797; famed as the \"Venice of Bengal\".",
    "examPointsBn": [
      "কীর্তনখোলা নদীর তীরে অবস্থিত ও “বাংলার ভেনিস” নামে পরিচিত",
      "শেরে বাংলা এ কে ফজলুল হক (চাখার) ও কবি জীবনানন্দ দাশের স্মৃতিবিজড়িত",
      "দুর্গাসাগর (দক্ষিণাঞ্চলের বৃহত্তম দিঘি) ও বরিশাল বিশ্ববিদ্যালয় (২০১১)",
      "বীরশ্রেষ্ঠ ক্যাপ্টেন মহিউদ্দীন জাহাঙ্গীরের জন্মস্থান (বাবুগঞ্জ)"
    ],
    "examPointsEn": [
      "Situated on the Kirtankhola River; known as the \"Venice of Bengal\"",
      "Associated with Sher-e-Bangla A.K. Fazlul Huq & poet Jibanananda Das",
      "Durgasagar Dighi & University of Barishal (2011)",
      "Birthplace of Bir Sreshtho Capt. Mohiuddin Jahangir"
    ]
  },
  "patuakhali": {
    "geoKey": "patuakhali",
    "divisionKey": "barishal",
    "nameBn": "পটুয়াখালী",
    "nameEn": "Patuakhali",
    "established": "1969",
    "areaKm2": 3221,
    "population": 1727254,
    "density": 536,
    "parliamentSeats": 4,
    "upazilaCount": 8,
    "govUrl": "https://patuakhali.gov.bd",
    "famousBn": "কুয়াকাটা (সাগরকন্যা), পায়রা সমুদ্রবন্দর, পায়রা তাপবিদ্যুৎ কেন্দ্র, পবিপ্রবি (PSTU)",
    "famousEn": "Kuakata (\"Daughter of the Sea\"), Payra Seaport, Payra Power Plant, PSTU",
    "historyBn": "১৯৬৯ সালে বাকেরগঞ্জ থেকে পৃথক হয়ে পটুয়াখালী জেলা গঠিত হয়। দেশের তৃতীয় সমুদ্রবন্দর পায়রা এখানে অবস্থিত।",
    "historyEn": "Established in 1969; home to Kuakata beach and Payra Seaport.",
    "examPointsBn": [
      "কুয়াকাটা সমুদ্রসৈকত (সাগরকন্যা)—একমাত্র সৈকত যেখান থেকে সূর্যোদয় ও সূর্যাস্ত দেখা যায়",
      "পায়রা সমুদ্রবন্দর—দেশের তৃতীয় সমুদ্রবন্দর (কলাপাড়া, রাবনাবাদ চ্যানেল)",
      "পায়রা ১৩২০ মেগাওয়াট তাপবিদ্যুৎ কেন্দ্র ও পবিপ্রবি (PSTU)"
    ],
    "examPointsEn": [
      "Kuakata (\"Daughter of the Sea\") — rare beach with both sunrise & sunset views",
      "Payra Seaport — Bangladesh’s 3rd seaport (Kalapara)",
      "Payra 1,320 MW Thermal Power Plant & PSTU university"
    ]
  },
  "bhola": {
    "geoKey": "bhola",
    "divisionKey": "barishal",
    "nameBn": "ভোলা",
    "nameEn": "Bhola",
    "established": "1984",
    "areaKm2": 3403,
    "population": 1932514,
    "density": 568,
    "parliamentSeats": 4,
    "upazilaCount": 7,
    "govUrl": "https://bhola.gov.bd",
    "famousBn": "বাংলাদেশের বৃহত্তম দ্বীপ, শাহবাজপুর গ্যাসক্ষেত্র, চর কুকরি-মুকরি, বীরশ্রেষ্ঠ মোস্তফা কামাল, মহিষের দই",
    "famousEn": "Largest island of Bangladesh, Shahbazpur Gas Field, Char Kukri-Mukri, Bir Sreshtho Mostafa Kamal",
    "historyBn": "১৯৮৪ সালে বরিশাল থেকে আলাদা হয়ে ভোলা জেলা হয়। এটি বাংলাদেশের বৃহত্তম বদ্বীপ ও একমাত্র দ্বীপ-জেলা।",
    "historyEn": "Separated from Barishal in 1984; Bangladesh’s largest island and only island district.",
    "examPointsBn": [
      "বাংলাদেশের বৃহত্তম দ্বীপ ও একমাত্র দ্বীপ-জেলা (ভোলা)",
      "শাহবাজপুর ও ভোলা নর্থ প্রাকৃতিক গ্যাসক্ষেত্র",
      "বীরশ্রেষ্ঠ সিপাহী মোহাম্মদ মোস্তফা কামালের জন্মস্থান (দৌলতখান)",
      "চর কুকরি-মুকরি বন্যপ্রাণী অভয়ারণ্য ও মনপুরা দ্বীপ"
    ],
    "examPointsEn": [
      "Bangladesh’s largest island and only island district",
      "Shahbazpur & Bhola North natural gas fields",
      "Birthplace of Bir Sreshtho Sepoy Mohammad Mostafa Kamal",
      "Char Kukri-Mukri Wildlife Sanctuary & Monpura Island"
    ]
  },
  "pirojpur": {
    "geoKey": "pirojpur",
    "divisionKey": "barishal",
    "nameBn": "পিরোজপুর",
    "nameEn": "Pirojpur",
    "established": "1984",
    "areaKm2": 1278,
    "population": 1198193,
    "density": 938,
    "parliamentSeats": 3,
    "upazilaCount": 7,
    "govUrl": "https://pirojpur.gov.bd",
    "famousBn": "স্বরূপকাঠির ভাসমান পেয়ারা বাগান ও নৌকা বাজার, রায়েরকাঠি জমিদার বাড়ি, বলেশ্বর ও কচা নদী",
    "famousEn": "Swarupkathi floating guava & boat market, Rayerkathi Palace, Kacha River",
    "historyBn": "১৯৮৪ সালে বরিশাল থেকে পৃথক হয়ে পিরোজপুর জেলা গঠিত হয়।",
    "historyEn": "Established as a district from Barishal in 1984.",
    "examPointsBn": [
      "স্বরূপকাঠি (নেছারাবাদ) পেয়ারা বাগান ও ঐতিহ্যবাহী কাঠের নৌকা শিল্প",
      "রায়েরকাঠি জমিদার বাড়ি ও বলেশ্বর-কচা নদী",
      "মঠবাড়িয়ার মমিন মসজিদ (কাঠের তৈরি প্রাচীন মসজিদ)"
    ],
    "examPointsEn": [
      "Swarupkathi guava orchards & traditional wooden boat craft",
      "Rayerkathi Zamindar Palace & Kacha/Baleshwar rivers",
      "Momin Mosque (historic all-wood mosque at Mathbaria)"
    ]
  },
  "barguna": {
    "geoKey": "barguna",
    "divisionKey": "barishal",
    "nameBn": "বরগুনা",
    "nameEn": "Barguna",
    "established": "1984",
    "areaKm2": 1831,
    "population": 1010530,
    "density": 552,
    "parliamentSeats": 2,
    "upazilaCount": 6,
    "govUrl": "https://barguna.gov.bd",
    "famousBn": "বিবিচিনি শাহী মসজিদ, টেংরাগিরি ইকোপার্ক, শুভসন্ধ্যা সমুদ্রসৈকত, পায়রা ও বলেশ্বর নদী",
    "famousEn": "Bibichini Shahi Mosque, Tengragiri Eco Park, Shubhosondhya Beach",
    "historyBn": "১৯৮৪ সালে পটুয়াখালী থেকে আলাদা হয়ে বরগুনা জেলা প্রতিষ্ঠিত হয়।",
    "historyEn": "Formed from Patuakhali in 1984 along the coastal river estuaries.",
    "examPointsBn": [
      "বিবিচিনি শাহী মসজিদ (মুঘল আমলের নিদর্শন, বেতাগী)",
      "টেংরাগিরি বন্যপ্রাণী অভয়ারণ্য (ফাতরার বন)",
      "পায়রা, বিষখালী ও বলেশ্বর নদী প্রবাহিত"
    ],
    "examPointsEn": [
      "Bibichini Shahi Mosque (Mughal-era monument, Betagi)",
      "Tengragiri Wildlife Sanctuary (Fatrar Bon)",
      "Payra, Bishkhali & Baleshwar estuaries"
    ]
  },
  "jhalokati": {
    "geoKey": "jhalokati",
    "divisionKey": "barishal",
    "nameBn": "ঝালকাঠি",
    "nameEn": "Jhalokati",
    "established": "1984",
    "areaKm2": 707,
    "population": 661161,
    "density": 935,
    "parliamentSeats": 2,
    "upazilaCount": 4,
    "govUrl": "https://jhalokati.gov.bd",
    "famousBn": "ভীমরুলি ভাসমান পেয়ারা বাজার, শীতলপাটি, সুগন্ধা নদী, ধানসিঁড়ি নদী, কীর্তিপাশা জমিদার বাড়ি",
    "famousEn": "Bhimruli Floating Guava Market, Shitalpati, Sugandha & Dhansiri Rivers",
    "historyBn": "১৯৮৪ সালে বরিশাল থেকে পৃথক হয়ে ঝালকাঠি জেলা হয়। ভাসমান পেয়ারা বাজার ও জীবনানন্দ দাশের ধানসিঁড়ি নদীর জন্য বিখ্যাত।",
    "historyEn": "Formed from Barishal in 1984; famous for Bhimruli floating guava market and Dhansiri River.",
    "examPointsBn": [
      "ভীমরুলি ভাসমান পেয়ারা বাজার (দক্ষিণ এশিয়ার অন্যতম বড় ভাসমান বাজার)",
      "কবি জীবনানন্দ দাশের বিখ্যাত ‘ধানসিঁড়ি’ নদী ঝালকাঠিতে প্রবাহিত",
      "শেরে বাংলা এ কে ফজলুল হকের জন্মস্থান (সাতুরিয়া, রাজাপুর)"
    ],
    "examPointsEn": [
      "Bhimruli Floating Guava Market",
      "Poet Jibanananda Das’s celebrated Dhansiri River flows here",
      "Birthplace of Sher-e-Bangla A.K. Fazlul Huq (Saturia, Rajapur)"
    ]
  },
  "sylhet": {
    "geoKey": "sylhet",
    "divisionKey": "sylhet",
    "nameBn": "সিলেট",
    "nameEn": "Sylhet",
    "established": "1782",
    "areaKm2": 3452,
    "population": 3857037,
    "density": 1117,
    "parliamentSeats": 6,
    "upazilaCount": 13,
    "govUrl": "https://sylhet.gov.bd",
    "famousBn": "চা বাগান (মালনীছড়া), জাফলং, রাতারগুল সোয়াম্প ফরেস্ট, হযরত শাহজালাল (র.) মাজার, শাবিপ্রবি (SUST), তামাবিল",
    "famousEn": "Tea Gardens (Malnicherra), Jaflong, Ratargul Swamp Forest, Shah Jalal Shrine, SUST",
    "historyBn": "১৭৮২ সালে প্রতিষ্ঠিত সিলেট \"দুটি পাতা একটি কুঁড়ির দেশ\" ও ৩৬০ আউলিয়ার পুণ্যভূমি হিসেবে খ্যাত।",
    "historyEn": "Established in 1782; famed as the land of tea gardens and 360 Sufi saints.",
    "examPointsBn": [
      "বাংলাদেশের প্রথম ও প্রাচীনতম চা বাগান—মালনীছড়া (১৮৫৪)",
      "রাতারগুল—বাংলাদেশের একমাত্র মিঠাপানির জলাবন (Swamp Forest)",
      "হরিপুর তেল ও গ্যাসক্ষেত্র (১৯৫৫—দেশের প্রথম গ্যাসক্ষেত্র) এবং তামাবিল স্থলবন্দর",
      "সিলেটের লালখাল/লালাখাল, সর্বাধিক বৃষ্টিপাত (লালাখাল/শ্রীমঙ্গল অঞ্চল), শাবিপ্রবি (SUST) ও ওসমানী আন্তর্জাতিক বিমানবন্দর"
    ],
    "examPointsEn": [
      "Malnicherra (1854) — first commercial tea garden in the subcontinent",
      "Ratargul — Bangladesh’s only freshwater swamp forest",
      "Haripur (1955) — Bangladesh’s first natural gas field & Tamabil Land Port",
      "Hosts SUST, Osmani International Airport & highest rainfall belt"
    ]
  },
  "sunamganj": {
    "geoKey": "sunamganj",
    "divisionKey": "sylhet",
    "nameBn": "সুনামগঞ্জ",
    "nameEn": "Sunamganj",
    "established": "1984",
    "areaKm2": 3747,
    "population": 2695495,
    "density": 719,
    "parliamentSeats": 5,
    "upazilaCount": 12,
    "govUrl": "https://sunamganj.gov.bd",
    "famousBn": "টাঙ্গুয়ার হাওর (রামসার সাইট), নীলাদ্রি লেক, যাদুকাটা নদী, ছাতক সিমেন্ট ফ্যাক্টরি, হাসন রাজা ও শাহ আবদুল করিম",
    "famousEn": "Tanguar Haor (Ramsar Site), Niladri Lake, Jadukata River, Chhatak Cement, Hason Raja",
    "historyBn": "১৯৮৪ সালে সিলেট থেকে আলাদা হয়ে সুনামগঞ্জ জেলা হয়। হাওর, বাউল সংগীত ও চুনাপাথর শিল্পের জন্য বিখ্যাত।",
    "historyEn": "Carved from Sylhet in 1984; renowned for Tanguar Haor wetlands and Baul folk music.",
    "examPointsBn": [
      "টাঙ্গুয়ার হাওর—বাংলাদেশের ২য় রামসার সাইট (২০০০ সাল, সুন্দরবনের পর)",
      "ছাতক সিমেন্ট ফ্যাক্টরি (১৯৪১)—বাংলাদেশের প্রথম সিমেন্ট কারখানা",
      "মরমী কবি হাসন রাজা, বাউল সম্রাট শাহ আবদুল করিম ও রাধারমণের জন্মস্থান",
      "সুরমা ও যাদুকাটা নদী এবং টেকেরঘাট চুনাপাথর খনি"
    ],
    "examPointsEn": [
      "Tanguar Haor — Bangladesh’s 2nd Ramsar Wetland Site (2000)",
      "Chhatak Cement Factory (1941) — oldest cement plant in Bangladesh",
      "Birthplace of Hason Raja & Baul Samrat Shah Abdul Karim",
      "Surma & Jadukata rivers + Takerghat limestone quarry"
    ]
  },
  "habiganj": {
    "geoKey": "habiganj",
    "divisionKey": "sylhet",
    "nameBn": "হবিগঞ্জ",
    "nameEn": "Habiganj",
    "established": "1984",
    "areaKm2": 2637,
    "population": 2358886,
    "density": 895,
    "parliamentSeats": 4,
    "upazilaCount": 9,
    "govUrl": "https://habiganj.gov.bd",
    "famousBn": "সাতছড়ি জাতীয় উদ্যান, রেমা-কালেঙ্গা বন্যপ্রাণী অভয়ারণ্য, বিবিয়ানা ও রশিদপুর গ্যাসক্ষেত্র, তেলিয়াপাড়া চা বাগান",
    "famousEn": "Satchari National Park, Rema-Kalenga Sanctuary, Bibiyana Gas Field, Teliapara",
    "historyBn": "১৯৮৪ সালে সিলেট থেকে পৃথক হয়ে হবিগঞ্জ জেলা গঠিত হয়। মুক্তিযুদ্ধের প্রথম সেনা সদর দপ্তর তেলিয়াপাড়ায় স্থাপিত হয়েছিল।",
    "historyEn": "Formed from Sylhet in 1984; hosts Bibiyana gas field and historic Teliapara Liberation War headquarters.",
    "examPointsBn": [
      "তেলিয়াপাড়া চা বাগান—১৯৭১ সালের ৪ এপ্রিল মুক্তিযুদ্ধের প্রথম সেনা বৈঠক ও সেক্টর বিভাজন",
      "বিবিয়ানা গ্যাসক্ষেত্র—দেশের অন্যতম বৃহৎ গ্যাস উৎপাদনকারী ক্ষেত্র",
      "সাতছড়ি জাতীয় উদ্যান ও রেমা-কালেঙ্গা বন্যপ্রাণী অভয়ারণ্য",
      "খোয়াই ও কালনী নদী প্রবাহিত"
    ],
    "examPointsEn": [
      "Teliapara Tea Estate — 4 April 1971 Liberation War command meeting",
      "Bibiyana Gas Field — largest active natural gas producing field",
      "Satchari National Park & Rema-Kalenga Wildlife Sanctuary",
      "Khowai & Kalni rivers"
    ]
  },
  "moulvibazar": {
    "geoKey": "moulvibazar",
    "divisionKey": "sylhet",
    "nameBn": "মৌলভীবাজার",
    "nameEn": "Moulvibazar",
    "established": "1984",
    "areaKm2": 2799,
    "population": 2123445,
    "density": 759,
    "parliamentSeats": 4,
    "upazilaCount": 7,
    "govUrl": "https://moulvibazar.gov.bd",
    "famousBn": "শ্রীমঙ্গল (চায়ের রাজধানী), লাউয়াছড়া জাতীয় উদ্যান, মাধবকুণ্ড জলপ্রপাত, হাকালুকি হাওর, মণিপুরী তাঁত",
    "famousEn": "Sreemangal (Tea Capital), Lawachara National Park, Madhabkunda Waterfall, Hakaluki Haor",
    "historyBn": "১৯৮৪ সালে সিলেট থেকে পৃথক হয়ে মৌলভীবাজার জেলা গঠিত হয়। দেশের অধিকাংশ চা বাগান এখানেই অবস্থিত।",
    "historyEn": "Separated from Sylhet in 1984; produces the majority of Bangladesh’s tea.",
    "examPointsBn": [
      "শ্রীমঙ্গল—বাংলাদেশের “চায়ের রাজধানী” ও বাংলাদেশ চা গবেষণা ইনস্টিটিউট (BTRI)",
      "মাধবকুণ্ড—বাংলাদেশের অন্যতম বৃহৎ প্রাকৃতিক জলপ্রপাত (বড়লেখা)",
      "লাউয়াছড়া জাতীয় উদ্যান (উল্লুকের অভয়ারণ্য) ও হাকালুকি হাওর (দেশের বৃহত্তম হাওর)",
      "বীরশ্রেষ্ঠ সিপাহী হামিদুর রহমানের সমাধি স্মৃতিসৌধ (কমলগঞ্জ)"
    ],
    "examPointsEn": [
      "Sreemangal — Tea Capital of Bangladesh & BTRI headquarters",
      "Madhabkunda — famous natural waterfall in Barlekha",
      "Lawachara National Park & Hakaluki Haor (largest haor)",
      "Monipuri handloom & Kamalganj tea estates"
    ]
  },
  "rangpur": {
    "geoKey": "rangpur",
    "divisionKey": "rangpur",
    "nameBn": "রংপুর",
    "nameEn": "Rangpur",
    "established": "1769",
    "areaKm2": 2401,
    "population": 3169615,
    "density": 1320,
    "parliamentSeats": 6,
    "upazilaCount": 8,
    "govUrl": "https://rangpur.gov.bd",
    "famousBn": "শতরঞ্জি (GI পণ্য), হাঁড়িভাঙ্গা আম (GI), তাজহাট জমিদার বাড়ি, বেগম রোকেয়া বিশ্ববিদ্যালয়, পায়রাবন্দ",
    "famousEn": "Shataranji (GI), Haribhanga Mango (GI), Tajhat Palace, Begum Rokeya University, Pairaband",
    "historyBn": "১৭৬৯ সালে প্রতিষ্ঠিত রংপুর প্রাচীন কামরূপের অংশ এবং নারী জাগরণের অগ্রদূত বেগম রোকেয়ার জন্মভূমি।",
    "historyEn": "Established in 1769; birthplace of women’s education pioneer Begum Rokeya and center of Rangpur Division.",
    "examPointsBn": [
      "রংপুরের শতরঞ্জি ও হাঁড়িভাঙ্গা আম বাংলাদেশের স্বীকৃত GI পণ্য",
      "বেগম রোকেয়া সাখাওয়াত হোসেনের জন্মস্থান (পায়রাবন্দ, মিঠাপুকুর)",
      "তাজহাট জমিদার বাড়ি জাদুঘর ও বেগম রোকেয়া বিশ্ববিদ্যালয় (BRUR)",
      "দেশের সর্বাধিক তামাক উৎপাদনকারী অঞ্চল ও তিস্তা নদী"
    ],
    "examPointsEn": [
      "Rangpur Shataranji & Haribhanga Mango (GI products)",
      "Birthplace of Begum Rokeya Sakhawat Hossain (Pairaband)",
      "Tajhat Palace Museum & Begum Rokeya University (BRUR)",
      "Major tobacco-producing region & Teesta River"
    ]
  },
  "nilphamari": {
    "geoKey": "nilphamari",
    "divisionKey": "rangpur",
    "nameBn": "নীলফামারী",
    "nameEn": "Nilphamari",
    "established": "1984",
    "areaKm2": 1547,
    "population": 3092567,
    "density": 1353,
    "parliamentSeats": 4,
    "upazilaCount": 6,
    "govUrl": "https://nilphamari.gov.bd",
    "famousBn": "উত্তরা EPZ, সৈয়দপুর বিমানবন্দর ও দেশের বৃহত্তম রেলওয়ে কারখানা, নীলসাগর, তিস্তা সেচ প্রকল্প",
    "famousEn": "Uttara EPZ, Saidpur Airport & Railway Workshop, Nilsagar",
    "historyBn": "ব্রিটিশ আমলের নীল চাষের ইতিহাস থেকে নামকরণকৃত নীলফামারী ১৯৮৪ সালে জেলা হয়।",
    "historyEn": "Named after colonial indigo (neel) plantations; established as a district in 1984.",
    "examPointsBn": [
      "সৈয়দপুর রেলওয়ে কারখানা (১৮৭০)—বাংলাদেশের বৃহত্তম রেলওয়ে ওয়ার্কশপ",
      "উত্তরা রপ্তানি প্রক্রিয়াকরণ অঞ্চল (Uttara EPZ) ও সৈয়দপুর অভ্যন্তরীণ বিমানবন্দর",
      "ঐতিহাসিক নীলসাগর দিঘি ও চিলাহাটি-হলদিবাড়ী রেলপথ"
    ],
    "examPointsEn": [
      "Saidpur Railway Workshop (1870) — largest railway workshop in Bangladesh",
      "Uttara EPZ & Saidpur domestic airport",
      "Historic Nilsagar Dighi & Chilahati-Haldibari rail link"
    ]
  },
  "dinajpur": {
    "geoKey": "dinajpur",
    "divisionKey": "rangpur",
    "nameBn": "দিনাজপুর",
    "nameEn": "Dinajpur",
    "established": "1786",
    "areaKm2": 3444,
    "population": 3315238,
    "density": 963,
    "parliamentSeats": 6,
    "upazilaCount": 13,
    "govUrl": "https://dinajpur.gov.bd",
    "famousBn": "কান্তজিউ মন্দির, রামসাগর দিঘি, বড়পুকুরিয়া কয়লাখনি, মধ্যপাড়া কঠিন শিলা, কাটারিভোগ চাল (GI), লিচু",
    "famousEn": "Kantajew Temple, Ramsagar Dighi, Barapukuria Coal Mine, Maddhapara Granite, Kataribhog Rice (GI)",
    "historyBn": "১৭৮৬ সালে প্রতিষ্ঠিত দিনাজপুর দেশের প্রধান খাদ্যশস্য ভাণ্ডার এবং একমাত্র সক্রিয় কয়লা ও কঠিন শিলা খনির জেলা।",
    "historyEn": "Established in 1786; major rice granary and home to Bangladesh’s active coal & hard-rock mines.",
    "examPointsBn": [
      "বড়পুকুরিয়া কয়লাখনি ও মধ্যপাড়া কঠিন শিলা খনি—দেশের একমাত্র সক্রিয় খনি",
      "কান্তজিউ মন্দির (১৭৫২, পোড়ামাটির টেরাকোটা শিল্পের শ্রেষ্ঠ নিদর্শন)",
      "দিনাজপুরের কাটারিভোগ চাল (GI পণ্য), লিচু ও রামসাগর জাতীয় উদ্যান",
      "হিলি স্থলবন্দর ও হাজী মোহাম্মদ দানেশ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (HSTU)"
    ],
    "examPointsEn": [
      "Barapukuria Coal Mine & Maddhapara Hard Rock Mine",
      "Kantajew Temple (1752) — finest terracotta temple in Bangladesh",
      "Dinajpur Kataribhog Rice (GI), Lychee & Ramsagar National Park",
      "Hili Land Port & HSTU university"
    ]
  },
  "thakurgaon": {
    "geoKey": "thakurgaon",
    "divisionKey": "rangpur",
    "nameBn": "ঠাকুরগাঁও",
    "nameEn": "Thakurgaon",
    "established": "1984",
    "areaKm2": 1781,
    "population": 1533894,
    "density": 861,
    "parliamentSeats": 3,
    "upazilaCount": 5,
    "govUrl": "https://thakurgaon.gov.bd",
    "famousBn": "বালিয়াডাঙ্গী সূর্যপুরী আমগাছ (এশিয়ার অন্যতম বড়), রাণীশংকৈল জমিদার বাড়ি, টাঙ্গন নদী, চিনিকল",
    "famousEn": "Baliadangi Giant Suryapuri Mango Tree, Ranisankail Palace, Tangon River",
    "historyBn": "১৯৮৪ সালে দিনাজপুর থেকে আলাদা হয়ে ঠাকুরগাঁও জেলা গঠিত হয়।",
    "historyEn": "Formed from Dinajpur in 1984; fertile northern agricultural district.",
    "examPointsBn": [
      "বালিয়াডাঙ্গীর ২০০ বছরের পুরনো বিশাল সূর্যপুরী আমগাছ (হরিণমারী)",
      "টাঙ্গন নদী ও ঠাকুরগাঁও সুগার মিলস",
      "তেভাগা আন্দোলনের ঐতিহাসিক স্মৃতিবিজড়িত অঞ্চল"
    ],
    "examPointsEn": [
      "200-year-old giant Suryapuri mango tree at Baliadangi",
      "Tangon River & Thakurgaon Sugar Mills",
      "Historic Tebhaga Movement region"
    ]
  },
  "gaibandha": {
    "geoKey": "gaibandha",
    "divisionKey": "rangpur",
    "nameBn": "গাইবান্ধা",
    "nameEn": "Gaibandha",
    "established": "1984",
    "areaKm2": 2115,
    "population": 2562232,
    "density": 1212,
    "parliamentSeats": 5,
    "upazilaCount": 7,
    "govUrl": "https://gaibandha.gov.bd",
    "famousBn": "বালাসী ঘাট, ফ্রেন্ডশিপ সেন্টার (আগা খান পুরস্কারপ্রাপ্ত স্থাপত্য), রসমঞ্জরী মিষ্টি, ব্রহ্মপুত্র ও তিস্তা",
    "famousEn": "Balashi Ghat, Friendship Centre (Aga Khan Award), Rasamanjari, Brahmaputra & Teesta",
    "historyBn": "১৯৮৪ সালে রংপুর থেকে পৃথক হয়ে গাইবান্ধা জেলা প্রতিষ্ঠিত হয়।",
    "historyEn": "Established as a district from Rangpur in 1984.",
    "examPointsBn": [
      "গাইবান্ধার ফ্রেন্ডশিপ সেন্টার (স্থপতি: কাশেফ মাহবুব চৌধুরী—২০১৬ আগা খান পুরস্কারপ্রাপ্ত)",
      "রসমঞ্জরী মিষ্টি ও বালাসী ঘাট",
      "তিস্তা ও ব্রহ্মপুত্র নদীর মিলনস্থলের নিকটবর্তী জেলা"
    ],
    "examPointsEn": [
      "Friendship Centre (Architect Kashef Chowdhury — 2016 Aga Khan Award)",
      "Famous Rasamanjari sweet & Balashi Ghat",
      "Near the Teesta-Brahmaputra confluence"
    ]
  },
  "kurigram": {
    "geoKey": "kurigram",
    "divisionKey": "rangpur",
    "nameBn": "কুড়িগ্রাম",
    "nameEn": "Kurigram",
    "established": "1984",
    "areaKm2": 2245,
    "population": 2329161,
    "density": 1037,
    "parliamentSeats": 4,
    "upazilaCount": 9,
    "govUrl": "https://kurigram.gov.bd",
    "famousBn": "চিলমারী নদীবন্দর, ব্রহ্মপুত্র প্রবেশপথ, সোনাহাট স্থলবন্দর, বীরপ্রতীক তারামন বিবি, ১৬টি নদী",
    "famousEn": "Chilmari River Port, Brahmaputra entry, Sonahat Land Port, Bir Protik Taramon Bibi",
    "historyBn": "১৯৮৪ সালে রংপুর থেকে পৃথক জেলা হয়। ব্রহ্মপুত্র, ধরলা ও তিস্তাসহ ১৬টি নদ-নদী এই জেলা দিয়ে প্রবাহিত।",
    "historyEn": "Formed from Rangpur in 1984; entry point of the Brahmaputra River into Bangladesh.",
    "examPointsBn": [
      "ব্রহ্মপুত্র নদ কুড়িগ্রাম জেলা দিয়ে বাংলাদেশে প্রবেশ করেছে",
      "মুক্তিযোদ্ধা বীরপ্রতীক তারামন বিবির জন্মস্থান (রাজিবপুর)",
      "চিলমারী ঐতিহাসিক নদীবন্দর ও সোনাহাট স্থলবন্দর"
    ],
    "examPointsEn": [
      "Brahmaputra River enters Bangladesh through Kurigram",
      "Home of Liberation War hero Bir Protik Taramon Bibi",
      "Chilmari historic river port & Sonahat Land Port"
    ]
  },
  "lalmonirhat": {
    "geoKey": "lalmonirhat",
    "divisionKey": "rangpur",
    "nameBn": "লালমনিরহাট",
    "nameEn": "Lalmonirhat",
    "established": "1984",
    "areaKm2": 1247,
    "population": 1428406,
    "density": 1145,
    "parliamentSeats": 3,
    "upazilaCount": 5,
    "govUrl": "https://lalmonirhat.gov.bd",
    "famousBn": "তিস্তা ব্যারেজ, বুড়িমারী স্থলবন্দর, তিন বিঘা করিডোর (দহগ্রাম-আঙ্গরপোতা), এভিয়েশন বিশ্ববিদ্যালয়",
    "famousEn": "Teesta Barrage, Burimari Land Port, Tin Bigha Corridor (Dahagram-Angorpota), BSMRAAU",
    "historyBn": "১৯৮৪ সালে রংপুর থেকে আলাদা হয়ে লালমনিরহাট জেলা গঠিত হয়।",
    "historyEn": "Carved from Rangpur in 1984 along the Teesta and Dharla rivers.",
    "examPointsBn": [
      "দহগ্রাম-আঙ্গরপোতা ও তিন বিঘা করিডোর (পাটগ্রাম)",
      "তিস্তা ব্যারেজ—বাংলাদেশের বৃহত্তম সেচ প্রকল্প (হাতীবান্ধা-ডালিয়া)",
      "বুড়িমারী স্থলবন্দর ও বঙ্গবন্ধু শেখ মুজিবুর রহমান এভিয়েশন অ্যান্ড অ্যারোস্পেস বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Dahagram-Angorpota & Tin Bigha Corridor (Patgram)",
      "Teesta Barrage — Bangladesh’s largest irrigation project",
      "Burimari Land Port & Aviation and Aerospace University"
    ]
  },
  "panchagarh": {
    "geoKey": "panchagarh",
    "divisionKey": "rangpur",
    "nameBn": "পঞ্চগড়",
    "nameEn": "Panchagarh",
    "established": "1980",
    "areaKm2": 1405,
    "population": 1179843,
    "density": 840,
    "parliamentSeats": 2,
    "upazilaCount": 5,
    "govUrl": "https://panchagarh.gov.bd",
    "famousBn": "বাংলাবান্ধা (সর্বউত্তরের স্থান ও জিরো পয়েন্ট), তেঁতুলিয়া, সমতল ভূমির চা বাগান, কাঞ্চনজঙ্ঘা দর্শন, ভিতরগড়",
    "famousEn": "Banglabandha (Northernmost point), Tetulia, Plain-land Tea Gardens, Kanchenjunga view, Bhitargarh",
    "historyBn": "১৯৮৪ সালে দিনাজপুর থেকে পৃথক জেলা হয়। হিমালয়ের পাদদেশে অবস্থিত বাংলাদেশের সর্বউত্তরের জেলা।",
    "historyEn": "Formed in 1984; northernmost district of Bangladesh at the Himalayan foothills.",
    "examPointsBn": [
      "বাংলাদেশের সর্বউত্তরের জেলা (পঞ্চগড়), উপজেলা (তেঁতুলিয়া) ও স্থান (বাংলাবান্ধা)",
      "বাংলাবান্ধা স্থলবন্দর—বাংলাদেশ, ভারত, নেপাল ও ভুটানের চতুর্দেশীয় বাণিজ্য পথ",
      "বাংলাদেশের একমাত্র সমতল ভূমির অর্গানিক চা বাগান (তেঁতুলিয়া)",
      "দেশের সর্বনিম্ন তাপমাত্রা রেকর্ড (২.৬° সে., ২০১৮) ও ভিতরগড় দুর্গনগরী"
    ],
    "examPointsEn": [
      "Northernmost district (Panchagarh), upazila (Tetulia) & point (Banglabandha)",
      "Banglabandha Land Port — 4-nation transit hub (BD-IN-NP-BT)",
      "Bangladesh’s plain-land organic tea gardens at Tetulia",
      "Lowest recorded temperature (2.6°C in 2018) & Bhitargarh fort city"
    ]
  },
  "mymensingh": {
    "geoKey": "mymensingh",
    "divisionKey": "mymensingh",
    "nameBn": "ময়মনসিংহ",
    "nameEn": "Mymensingh",
    "established": "1787",
    "areaKm2": 4395,
    "population": 5899052,
    "density": 1342,
    "parliamentSeats": 11,
    "upazilaCount": 13,
    "govUrl": "https://mymensingh.gov.bd",
    "famousBn": "বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU), শশী লজ, শিল্পাচার্য জয়নুল আবেদিন সংগ্রহশালা, পুরাতন ব্রহ্মপুত্র, মৈমনসিংহ গীতিকা",
    "famousEn": "Bangladesh Agricultural University (BAU), Shashi Lodge, Zainul Abedin Museum, Old Brahmaputra",
    "historyBn": "১৭৮৭ সালে প্রতিষ্ঠিত বৃহত্তর ময়মনসিংহ ২০১৫ সালে বাংলাদেশের ৮ম (সর্বশেষ) বিভাগে উন্নীত হয়।",
    "historyEn": "Established in 1787; became headquarters of Bangladesh’s 8th (newest) division in 2015.",
    "examPointsBn": [
      "বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU, ১৯৬১)—দক্ষিণ এশিয়ার অন্যতম বৃহৎ কৃষি বিশ্ববিদ্যালয়",
      "শিল্পাচার্য জয়নুল আবেদিন সংগ্রহশালা ও শশী লজ (মুক্তাগাছার মণ্ডা GI)",
      "বাংলাদেশ মৎস্য গবেষণা ইনস্টিটিউট (BFRI) ও পরমাণু কৃষি গবেষণা ইনস্টিটিউট (BINA)",
      "জাতীয় কবি কাজী নজরুল ইসলাম বিশ্ববিদ্যালয় (ত্রিশাল)"
    ],
    "examPointsEn": [
      "Bangladesh Agricultural University (BAU, 1961)",
      "Zainul Abedin Art Museum, Shashi Lodge & Muktagachha Monda (GI)",
      "Headquarters of BFRI & BINA research institutes",
      "Jatiya Kabi Kazi Nazrul Islam University (Trishal)"
    ]
  },
  "jamalpur": {
    "geoKey": "jamalpur",
    "divisionKey": "mymensingh",
    "nameBn": "জামালপুর",
    "nameEn": "Jamalpur",
    "established": "1978",
    "areaKm2": 2115,
    "population": 2499737,
    "density": 1182,
    "parliamentSeats": 5,
    "upazilaCount": 7,
    "govUrl": "https://jamalpur.gov.bd",
    "famousBn": "নকশিকাঁথা (GI পণ্য), যমুনা সার কারখানা (তারাকান্দি), লাউচাপড়া পিকনিক স্পট, বশেফমুবিপ্রবি",
    "famousEn": "Nakshi Kantha (GI), Jamuna Fertilizer Factory, Lauchapra, BSFMSTU",
    "historyBn": "১৯৭৮ সালে ময়মনসিংহ থেকে পৃথক হয়ে জামালপুর জেলা গঠিত হয়। ঐতিহ্যবাহী নকশিকাঁথার জন্য বিশ্বখ্যাত।",
    "historyEn": "Carved from Mymensingh in 1978; famous for traditional Nakshi Kantha embroidery.",
    "examPointsBn": [
      "জামালপুরের ঐতিহ্যবাহী নকশিকাঁথা (বাংলাদেশের স্বীকৃত GI পণ্য)",
      "যমুনা সার কারখানা (তারাকান্দি)—দেশের অন্যতম বৃহৎ ইউরিয়া সার কারখানা",
      "বকশীগঞ্জের ধানুয়া কামালপুর মুক্তিযুদ্ধের ঐতিহাসিক রণাঙ্গন"
    ],
    "examPointsEn": [
      "Jamalpur Nakshi Kantha (recognized GI craft)",
      "Jamuna Fertilizer Factory at Tarakandi",
      "Dhanua Kamalpur historic Liberation War battleground"
    ]
  },
  "netrokona": {
    "geoKey": "netrokona",
    "divisionKey": "mymensingh",
    "nameBn": "নেত্রকোণা",
    "nameEn": "Netrokona",
    "established": "1984",
    "areaKm2": 2794,
    "population": 2324856,
    "density": 832,
    "parliamentSeats": 5,
    "upazilaCount": 10,
    "govUrl": "https://netrokona.gov.bd",
    "famousBn": "বিরিশিরি চিনামাটির পাহাড় (সাদামাটি), সোমেশ্বরী নদী, বালিশ মিষ্টি (GI), গারো পাহাড়, সুসং দুর্গাপুর",
    "famousEn": "Birishiri White Clay Hills, Someshwari River, Balish Mishti (GI), Garo Hills",
    "historyBn": "১৯৮৪ সালে ময়মনসিংহ থেকে পৃথক জেলা হয়। বিজয়পুরের চিনামাটির পাহাড় ও হাওরের জন্য অনন্য।",
    "historyEn": "Formed from Mymensingh in 1984; famous for Birishiri white clay hills and Balish sweet.",
    "examPointsBn": [
      "বিজয়পুর (দুর্গাপুর)—বাংলাদেশের প্রধান সাদামাটি/চিনামাটির খনি অঞ্চল",
      "নেত্রকোণার বিখ্যাত বালিশ মিষ্টি (GI পণ্য) ও সোমেশ্বরী নদী",
      "টঙ্ক আন্দোলনের ঐতিহাসিক কেন্দ্র (কমরেড মণি সিংহ) ও শেখ হাসিনা বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Bijoypur (Durgapur) — Bangladesh’s main white china-clay hill reserve",
      "Netrokona Balish Mishti (GI product) & Someshwari River",
      "Historic Tonk Movement center & Sheikh Hasina University"
    ]
  },
  "sherpur": {
    "geoKey": "sherpur",
    "divisionKey": "mymensingh",
    "nameBn": "শেরপুর",
    "nameEn": "Sherpur",
    "established": "1984",
    "areaKm2": 1365,
    "population": 1501853,
    "density": 1101,
    "parliamentSeats": 3,
    "upazilaCount": 5,
    "govUrl": "https://sherpur.gov.bd",
    "famousBn": "গজনী অবকাশ কেন্দ্র, মধুটিলা ইকোপার্ক, তুলশীমালা চাল (GI পণ্য), গারো পাহাড়, নাকুগাঁও স্থলবন্দর",
    "famousEn": "Gajni Leisure Center, Madhutilla Eco Park, Tulshimala Rice (GI), Nakugaon Land Port",
    "historyBn": "১৯৮৪ সালে জামালপুর থেকে আলাদা হয়ে শেরপুর জেলা প্রতিষ্ঠিত হয়। গারো পাহাড়ের পাদদেশে অবস্থিত।",
    "historyEn": "Formed from Jamalpur in 1984 at the foothills of the Garo Hills.",
    "examPointsBn": [
      "শেরপুরের তুলশীমালা চাল (বাংলাদেশের স্বীকৃত GI পণ্য)",
      "গজনী অবকাশ কেন্দ্র ও মধুটিলা ইকোপার্ক (গারো পাহাড়)",
      "নাকুগাঁও স্থলবন্দর (মেঘালয়ের সাথে সংযুক্ত) ও সোহাগপুর বিধবাপল্লী"
    ],
    "examPointsEn": [
      "Sherpur Tulshimala aromatic rice (GI product)",
      "Gajni & Madhutilla eco-tourism parks in the Garo Hills",
      "Nakugaon Land Port connecting to Meghalaya, India"
    ]
  }
};

export const BD_RIVERS = [
  {
    "id": "padma",
    "nameBn": "পদ্মা নদী",
    "nameEn": "Padma River",
    "lengthKm": "৩৬৬ কি.মি. (বাংলাদেশে)",
    "lengthKmEn": "366 km (in Bangladesh)",
    "originBn": "হিমালয়ের গঙ্গোত্রী হিমবাহ (গঙ্গা নামে উৎপত্তি, চাঁপাইনবাবগঞ্জ দিয়ে প্রবেশ)",
    "originEn": "Gangotri Glacier in the Himalayas (enters as Ganges via Chapainawabganj)",
    "mouthBn": "চাঁদপুরে মেঘনা নদীর সাথে মিলিত হয়ে বঙ্গোপসাগরে",
    "mouthEn": "Meets Meghna at Chandpur, emptying into the Bay of Bengal",
    "districtsBn": "চাঁপাইনবাবগঞ্জ, রাজশাহী, নাটোর, পাবনা, কুষ্টিয়া, রাজবাড়ী, মানিকগঞ্জ, ফরিদপুর, মুন্সীগঞ্জ, শরীয়তপুর, মাদারীপুর, চাঁদপুর",
    "districtsEn": "Chapainawabganj, Rajshahi, Natore, Pabna, Kushtia, Rajbari, Manikganj, Faridpur, Munshiganj, Shariatpur, Madaripur, Chandpur",
    "tributariesBn": "উপনদী: মহানন্দা; শাখানদী: গড়াই, মধুমতী, মাথাভাঙ্গা, আড়িয়াল খাঁ, কুমার",
    "tributariesEn": "Tributary: Mahananda; Distributaries: Gorai, Madhumati, Mathabhanga, Arial Khan, Kumar",
    "summaryBn": "বাংলাদেশের অন্যতম প্রধান ও প্রশস্ত নদী। গোয়ালন্দে যমুনার সাথে এবং চাঁদপুরে মেঘনার সাথে মিলিত হয়েছে।",
    "summaryEn": "One of Bangladesh’s primary distributary rivers; joins the Jamuna at Goalanda and the Meghna at Chandpur.",
    "examPointsBn": [
      "গঙ্গা নদী চাঁপাইনবাবগঞ্জের শিবগঞ্জ দিয়ে বাংলাদেশে প্রবেশ করে পদ্মা নাম ধারণ করে",
      "পদ্মা ও যমুনা মিলিত হয়েছে গোয়ালন্দে (রাজবাড়ী) এবং পদ্মা ও মেঘনা মিলিত হয়েছে চাঁদপুরে",
      "পদ্মা বহুমুখী সেতু (৬.১৫ কি.মি., মাওয়া–জাজিরা) এবং হার্ডিঞ্জ ব্রিজ (১৯১৫) পদ্মার ওপর নির্মিত",
      "পদ্মার একমাত্র উল্লেখযোগ্য উপনদী মহানন্দা"
    ],
    "examPointsEn": [
      "Ganges enters Bangladesh at Shibganj (Chapainawabganj) and becomes the Padma",
      "Meets Jamuna at Goalanda (Rajbari) and Meghna at Chandpur",
      "Spanned by Padma Multipurpose Bridge (6.15 km) and Hardinge Bridge (1915)",
      "Mahananda is its only major tributary inside Bangladesh"
    ],
    "coords": [
      [
        88.08,
        24.65
      ],
      [
        88.35,
        24.42
      ],
      [
        88.6,
        24.36
      ],
      [
        88.95,
        24.16
      ],
      [
        89.08,
        24.06
      ],
      [
        89.38,
        23.95
      ],
      [
        89.76,
        23.8
      ],
      [
        90.02,
        23.62
      ],
      [
        90.26,
        23.46
      ],
      [
        90.48,
        23.34
      ],
      [
        90.64,
        23.23
      ]
    ]
  },
  {
    "id": "jamuna",
    "nameBn": "যমুনা নদী",
    "nameEn": "Jamuna River",
    "lengthKm": "২০৫ কি.মি.",
    "lengthKmEn": "205 km",
    "originBn": "তিব্বতের মানস সরোবর (ব্রহ্মপুত্রের মূল ধারা, কুড়িগ্রাম দিয়ে প্রবেশ)",
    "originEn": "Lake Manasarovar, Tibet (main channel of Brahmaputra entering via Kurigram)",
    "mouthBn": "রাজবাড়ীর গোয়ালন্দে পদ্মা নদীর সাথে মিলিত",
    "mouthEn": "Joins the Padma River at Goalanda (Rajbari)",
    "districtsBn": "কুড়িগ্রাম, গাইবান্ধা, জামালপুর, বগুড়া, সিরাজগঞ্জ, টাঙ্গাইল, পাবনা, মানিকগঞ্জ",
    "districtsEn": "Kurigram, Gaibandha, Jamalpur, Bogura, Sirajganj, Tangail, Pabna, Manikganj",
    "tributariesBn": "উপনদী: তিস্তা, ধরলা, করতোয়া, আত্রাই; শাখানদী: ধলেশ্বরী, বুড়িগঙ্গা",
    "tributariesEn": "Tributaries: Teesta, Dharla, Karatoya, Atrai; Distributaries: Dhaleshwari, Buriganga",
    "summaryBn": "১৭৮৭ সালের ভূমিকম্প ও বন্যার পর ব্রহ্মপুত্রের প্রধান প্রবাহ যমুনা নামে দক্ষিণে প্রবাহিত হয়। এটি বাংলাদেশের সবচেয়ে প্রশস্ত নদী।",
    "summaryEn": "Formed after the 1787 earthquake shifted the Brahmaputra’s main course southward; widest braided river in Bangladesh.",
    "examPointsBn": [
      "বাংলাদেশের সবচেয়ে প্রশস্ত নদী—যমুনা",
      "বঙ্গবন্ধু যমুনা বহুমুখী সেতু (৪.৮ কি.মি., ১৯৯৮) টাঙ্গাইল ও সিরাজগঞ্জকে যুক্ত করেছে",
      "যমুনার প্রধান উপনদী তিস্তা এবং প্রধান শাখানদী ধলেশ্বরী",
      "গোয়ালন্দে পদ্মার সাথে মিলিত হয়েছে"
    ],
    "examPointsEn": [
      "Widest river in Bangladesh (braided channel)",
      "Jamuna Multipurpose Bridge (4.8 km, 1998) connects Tangail & Sirajganj",
      "Major tributary: Teesta; major distributary: Dhaleshwari",
      "Merges with the Padma at Goalanda"
    ],
    "coords": [
      [
        89.8,
        25.88
      ],
      [
        89.73,
        25.45
      ],
      [
        89.68,
        25.05
      ],
      [
        89.76,
        24.68
      ],
      [
        89.77,
        24.38
      ],
      [
        89.74,
        24.08
      ],
      [
        89.76,
        23.8
      ]
    ]
  },
  {
    "id": "meghna",
    "nameBn": "মেঘনা নদী",
    "nameEn": "Meghna River",
    "lengthKm": "৩৩০ কি.মি. (সুরমাসহ ৯৪৬ কি.মি.)",
    "lengthKmEn": "330 km (946 km with Surma)",
    "originBn": "সুরমা ও কুশিয়ারা নদীর মিলিত ধারা (ভৈরব বাজার / আজমিরীগঞ্জ)",
    "originEn": "Confluence of Surma and Kushiyara rivers (Ajmiriganj / Bhairab Bazar)",
    "mouthBn": "ভোলা ও নোয়াখালী হয়ে বঙ্গোপসাগরে পতিত",
    "mouthEn": "Empties into the Bay of Bengal via Bhola & Noakhali",
    "districtsBn": "কিশোরগঞ্জ, ব্রাহ্মণবাড়িয়া, নরসিংদী, নারায়ণগঞ্জ, মুন্সীগঞ্জ, কুমিল্লা, চাঁদপুর, লক্ষ্মীপুর, ভোলা, নোয়াখালী",
    "districtsEn": "Kishoreganj, Brahmanbaria, Narsingdi, Narayanganj, Munshiganj, Cumilla, Chandpur, Lakshmipur, Bhola, Noakhali",
    "tributariesBn": "উপনদী: তিতাস, গোমতী, শীতলক্ষ্যা, ধলেশ্বরী, ডাকাতিয়া",
    "tributariesEn": "Tributaries: Titas, Gumti, Shitalakshya, Dhaleshwari, Dakatia",
    "summaryBn": "বাংলাদেশের দীর্ঘতম ও গভীরতম নদী ব্যবস্থা। চাঁদপুরে পদ্মার সাথে মিলিত হয়ে নিম্ন মেঘনা নামে বঙ্গোপসাগরে পড়েছে।",
    "summaryEn": "Deepest and longest river system in Bangladesh; merges with Padma at Chandpur to form the Lower Meghna estuary.",
    "examPointsBn": [
      "বাংলাদেশের দীর্ঘতম নদী—সুরমা-মেঘনা ব্যবস্থা এবং গভীরতম নদী—মেঘনা",
      "সুরমা ও কুশিয়ারা মিলিত হয়ে কালনী ও পরে ভৈরবে মেঘনা নাম ধারণ করে",
      "চাঁদপুরে পদ্মা ও মেঘনার মিলনস্থল এবং মেঘনা-গোমতী সেতু গুরুত্বপূর্ণ",
      "মেঘনার মোহনায় বাংলাদেশের বৃহত্তম দ্বীপ ভোলা ও নিঝুম দ্বীপ অবস্থিত"
    ],
    "examPointsEn": [
      "Deepest river in Bangladesh; longest river system with Surma-Meghna",
      "Formed by the confluence of Surma & Kushiyara rivers",
      "Joins Padma at Chandpur before discharging into the Bay of Bengal",
      "Bhola (largest island) & Nijhum Dwip lie in the Meghna estuary"
    ],
    "coords": [
      [
        91.22,
        24.48
      ],
      [
        91,
        24.18
      ],
      [
        90.98,
        24.04
      ],
      [
        90.8,
        23.82
      ],
      [
        90.65,
        23.56
      ],
      [
        90.64,
        23.23
      ],
      [
        90.68,
        22.85
      ],
      [
        90.82,
        22.4
      ]
    ]
  },
  {
    "id": "old-brahmaputra",
    "nameBn": "পুরাতন ব্রহ্মপুত্র নদ",
    "nameEn": "Old Brahmaputra River",
    "lengthKm": "২৭৬ কি.মি.",
    "lengthKmEn": "276 km",
    "originBn": "জামালপুরের দেওয়ানগঞ্জে যমুনা থেকে উৎপন্ন",
    "originEn": "Branches off Jamuna near Dewanganj (Jamalpur)",
    "mouthBn": "ভৈরব বাজারে মেঘনা নদীতে মিলিত",
    "mouthEn": "Joins the Meghna River at Bhairab Bazar",
    "districtsBn": "জামালপুর, শেরপুর, ময়মনসিংহ, কিশোরগঞ্জ, নরসিংদী",
    "districtsEn": "Jamalpur, Sherpur, Mymensingh, Kishoreganj, Narsingdi",
    "tributariesBn": "শাখানদী: শীতলক্ষ্যা, বানার",
    "tributariesEn": "Distributaries: Shitalakshya, Banar",
    "summaryBn": "১৭৮৭ সালের আগে এটিই ছিল ব্রহ্মপুত্রের মূল প্রবাহ। ময়মনসিংহ শহর ও বাংলাদেশ কৃষি বিশ্ববিদ্যালয় এই নদের তীরে অবস্থিত।",
    "summaryEn": "Original main course of the Brahmaputra prior to 1787; Mymensingh city and BAU stand on its banks.",
    "examPointsBn": [
      "ময়মনসিংহ শহর ও বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU) পুরাতন ব্রহ্মপুত্রের তীরে",
      "ভৈরব বাজারে মেঘনা নদীর সাথে মিলিত হয়েছে",
      "ব্রহ্মপুত্র নদ বাংলাদেশে কুড়িগ্রাম জেলা দিয়ে প্রবেশ করেছে"
    ],
    "examPointsEn": [
      "Mymensingh city & BAU are situated on its banks",
      "Meets the Meghna River at Bhairab Bazar",
      "Brahmaputra enters Bangladesh through Kurigram"
    ],
    "coords": [
      [
        89.76,
        25.18
      ],
      [
        90.02,
        24.92
      ],
      [
        90.41,
        24.75
      ],
      [
        90.65,
        24.42
      ],
      [
        90.98,
        24.04
      ]
    ]
  },
  {
    "id": "teesta",
    "nameBn": "তিস্তা নদী",
    "nameEn": "Teesta River",
    "lengthKm": "১১৩ কি.মি. (বাংলাদেশে)",
    "lengthKmEn": "113 km (in Bangladesh)",
    "originBn": "সিকিমের পাউহুনরি হিমবাহ (নীলফামারী দিয়ে বাংলাদেশে প্রবেশ)",
    "originEn": "Pahunri Glacier in Sikkim (enters via Nilphamari)",
    "mouthBn": "কুড়িগ্রামের চিলমারীতে ব্রহ্মপুত্র-যমুনায় মিলিত",
    "mouthEn": "Joins Brahmaputra-Jamuna near Chilmari (Kurigram)",
    "districtsBn": "নীলফামারী, লালমনিরহাট, রংপুর, কুড়িগ্রাম, গাইবান্ধা",
    "districtsEn": "Nilphamari, Lalmonirhat, Rangpur, Kurigram, Gaibandha",
    "tributariesBn": "তিস্তা ব্যারেজ (ডালিয়া-হাতীবান্ধা) উত্তরবঙ্গের প্রধান সেচ উৎস",
    "tributariesEn": "Teesta Barrage (Dalia) is North Bengal’s primary irrigation lifeline",
    "summaryBn": "উত্তরবঙ্গের প্রাণরেখা হিসেবে পরিচিত আন্তঃসীমান্ত নদী। তিস্তা পানি বণ্টন চুক্তি বাংলাদেশ-ভারত সম্পর্কের অন্যতম প্রধান আলোচ্য বিষয়।",
    "summaryEn": "Lifeline transboundary river of Northern Bangladesh; central to Bangladesh-India water-sharing diplomacy.",
    "examPointsBn": [
      "তিস্তা ব্যারেজ (হাতীবান্ধা, লালমনিরহাট)—বাংলাদেশের বৃহত্তম সেচ প্রকল্প",
      "ভারত-বাংলাদেশ অভিন্ন ৫৪টি নদীর মধ্যে তিস্তা পানি বণ্টন সবচেয়ে আলোচিত",
      "কুড়িগ্রামের চিলমারীর কাছে ব্রহ্মপুত্র-যমুনায় মিলিত হয়েছে"
    ],
    "examPointsEn": [
      "Teesta Barrage (Lalmonirhat) — Bangladesh’s largest irrigation project",
      "Key transboundary river among 54 shared Bangladesh-India rivers",
      "Merges with the Brahmaputra-Jamuna near Chilmari"
    ],
    "coords": [
      [
        88.95,
        26.28
      ],
      [
        89.1,
        26.12
      ],
      [
        89.38,
        25.88
      ],
      [
        89.58,
        25.68
      ],
      [
        89.7,
        25.54
      ]
    ]
  },
  {
    "id": "surma",
    "nameBn": "সুরমা নদী",
    "nameEn": "Surma River",
    "lengthKm": "২৪৯ কি.মি.",
    "lengthKmEn": "249 km",
    "originBn": "আসামের বরাক নদী সিলেটের অমলশিদে দুই ভাগে বিভক্ত হয়ে উত্তর ধারা সুরমা",
    "originEn": "Northern branch of Barak River splitting at Amalshid (Sylhet)",
    "mouthBn": "সুনামগঞ্জ ও কিশোরগঞ্জ হয়ে মেঘনায় মিলিত",
    "mouthEn": "Merges with Kushiyara/Kalni into the Meghna",
    "districtsBn": "সিলেট, সুনামগঞ্জ, কিশোরগঞ্জ",
    "districtsEn": "Sylhet, Sunamganj, Kishoreganj",
    "tributariesBn": "উপনদী: পিয়াইন, সারি-গোয়াইন, যাদুকাটা",
    "tributariesEn": "Tributaries: Piyain, Sari-Goyain, Jadukata",
    "summaryBn": "সিলেট ও সুনামগঞ্জ শহর সুরমা নদীর তীরে অবস্থিত। বরাক নদীর উত্তর শাখা হিসেবে এটি সিলেট অঞ্চলের প্রধান নদী।",
    "summaryEn": "Northern distributary of the Barak River; flows through Sylhet and Sunamganj cities.",
    "examPointsBn": [
      "বরাক নদী সিলেটের জকিগঞ্জের অমলশিদে সুরমা ও কুশিয়ারা নামে দুই ভাগে বিভক্ত হয়",
      "সিলেট শহর, ক্বীন ব্রিজ ও সুনামগঞ্জ শহর সুরমা নদীর তীরে অবস্থিত",
      "সুরমা-মেঘনা মিলিতভাবে বাংলাদেশের দীর্ঘতম নদী প্রণালী"
    ],
    "examPointsEn": [
      "Barak splits into Surma and Kushiyara at Amalshid, Zakiganj (Sylhet)",
      "Sylhet city, Keane Bridge & Sunamganj lie on the Surma",
      "Surma-Meghna forms Bangladesh’s longest river system"
    ],
    "coords": [
      [
        92.48,
        24.88
      ],
      [
        92.18,
        24.92
      ],
      [
        91.87,
        24.89
      ],
      [
        91.4,
        25.07
      ],
      [
        91.18,
        24.8
      ],
      [
        91.22,
        24.48
      ]
    ]
  },
  {
    "id": "kushiyara",
    "nameBn": "কুশিয়ারা নদী",
    "nameEn": "Kushiyara River",
    "lengthKm": "১৬১ কি.মি.",
    "lengthKmEn": "161 km",
    "originBn": "বরাক নদীর দক্ষিণ শাখা (অমলশিদ, জকিগঞ্জ)",
    "originEn": "Southern branch of Barak River at Amalshid (Sylhet)",
    "mouthBn": "আজমিরীগঞ্জে কালনী/সুরমার সাথে মিলিত হয়ে মেঘনা",
    "mouthEn": "Rejoins Surma/Kalni at Ajmiriganj to form Meghna",
    "districtsBn": "সিলেট, মৌলভীবাজার, হবিগঞ্জ, সুনামগঞ্জ",
    "districtsEn": "Sylhet, Moulvibazar, Habiganj, Sunamganj",
    "tributariesBn": "উপনদী: মনু, খোয়াই",
    "tributariesEn": "Tributaries: Manu, Khowai",
    "summaryBn": "সিলেট-ভারত সীমান্ত ঘেঁষে প্রবাহিত বরাকের দক্ষিণ শাখা। ২০২২ সালে বাংলাদেশ-ভারত কুশিয়ারা পানি প্রত্যাহার সমঝোতা স্বাক্ষরিত হয়।",
    "summaryEn": "Southern branch of Barak; subject of the 2022 Bangladesh-India water-sharing MoU.",
    "examPointsBn": [
      "২০২২ সালে বাংলাদেশ ও ভারতের মধ্যে কুশিয়ারা নদীর পানি বণ্টন সমঝোতা স্মারক স্বাক্ষরিত হয়",
      "ফেঞ্চুগঞ্জ সার কারখানা ও শেরপুর সেতু কুশিয়ারা নদীর তীরে"
    ],
    "examPointsEn": [
      "Bangladesh-India signed the Kushiyara River water MoU in September 2022",
      "Fenchuganj Fertilizer Factory stands along the Kushiyara"
    ],
    "coords": [
      [
        92.48,
        24.88
      ],
      [
        92.12,
        24.78
      ],
      [
        91.9,
        24.69
      ],
      [
        91.55,
        24.62
      ],
      [
        91.22,
        24.48
      ]
    ]
  },
  {
    "id": "karnaphuli",
    "nameBn": "কর্ণফুলী নদী",
    "nameEn": "Karnaphuli River",
    "lengthKm": "২৭০ কি.মি.",
    "lengthKmEn": "270 km",
    "originBn": "ভারতের মিজোরামের লুসাই পাহাড়",
    "originEn": "Lushai Hills of Mizoram, India",
    "mouthBn": "পতেঙ্গা (চট্টগ্রাম) হয়ে বঙ্গোপসাগরে",
    "mouthEn": "Bay of Bengal at Patenga (Chattogram)",
    "districtsBn": "রাঙামাটি, চট্টগ্রাম",
    "districtsEn": "Rangamati, Chattogram",
    "tributariesBn": "উপনদী: হালদা, কাসালং, রাইংখ্যং, বোয়ালখালী",
    "tributariesEn": "Tributaries: Halda, Kasalong, Rainkhyang",
    "summaryBn": "চট্টগ্রাম অঞ্চলের প্রধান নদী। দেশের প্রধান সমুদ্রবন্দর, কাপ্তাই জলবিদ্যুৎ কেন্দ্র ও দেশের প্রথম তলদেশীয় টানেল এই নদীতে।",
    "summaryEn": "Principal river of Chattogram; home to Chattogram Seaport, Kaptai Dam, and South Asia’s first underwater road tunnel.",
    "examPointsBn": [
      "চট্টগ্রাম সমুদ্রবন্দর ও কাপ্তাই জলবিদ্যুৎ কেন্দ্র কর্ণফুলী নদীর তীরে",
      "কর্ণফুলী নদীর তলদেশে নির্মিত দেশের প্রথম ও দক্ষিণ এশিয়ার প্রথম রোড টানেল (৩.৩২ কি.মি.)",
      "উৎপত্তিস্থল: ভারতের মিজোরামের লুসাই পাহাড়",
      "হালদা নদী কর্ণফুলীর সবচেয়ে বিখ্যাত উপনদী"
    ],
    "examPointsEn": [
      "Chattogram Seaport & Kaptai Hydroelectric Plant sit on the Karnaphuli",
      "Site of Bangladesh & South Asia’s first underwater road tunnel (3.32 km)",
      "Originates in the Lushai Hills of Mizoram, India",
      "Halda River is its most famous tributary"
    ],
    "coords": [
      [
        92.42,
        22.82
      ],
      [
        92.22,
        22.48
      ],
      [
        92.04,
        22.42
      ],
      [
        91.88,
        22.34
      ],
      [
        91.79,
        22.23
      ]
    ]
  },
  {
    "id": "halda",
    "nameBn": "হালদা নদী",
    "nameEn": "Halda River",
    "lengthKm": "৯৮ কি.মি.",
    "lengthKmEn": "98 km",
    "originBn": "খাগড়াছড়ির রামগড়ের বাদনাতলী পাহাড়",
    "originEn": "Badnatali Hills in Ramgarh (Khagrachhari)",
    "mouthBn": "চট্টগ্রামের কালুরঘাটে কর্ণফুলী নদীতে",
    "mouthEn": "Joins the Karnaphuli River at Kalurghat (Chattogram)",
    "districtsBn": "খাগড়াছড়ি, চট্টগ্রাম",
    "districtsEn": "Khagrachhari, Chattogram",
    "tributariesBn": "সম্পূর্ণ বাংলাদেশের ভেতরে উৎপন্ন ও সমাপ্ত",
    "tributariesEn": "Originates and ends entirely within Bangladesh",
    "summaryBn": "বিশ্বের একমাত্র জোয়ার-ভাটাপ্রবণ প্রাকৃতিক কার্প জাতীয় মাছের (রুই, কাতলা, মৃগেল) প্রজনন ক্ষেত্র এবং বঙ্গবন্ধু মৎস্য হেরিটেজ।",
    "summaryEn": "World’s only tidal natural carp spawning river and declared a national Fisheries Heritage site.",
    "examPointsBn": [
      "এশিয়ার একমাত্র প্রাকৃতিক মৎস্য প্রজনন ক্ষেত্র (রুই, কাতলা, মৃগেল রেণু পোনা)",
      "২০২০ সালে হালদা নদীকে “বঙ্গবন্ধু মৎস্য হেরিটেজ” ঘোষণা করা হয়",
      "উৎপত্তি ও সমাপ্তি দুটিই বাংলাদেশের অভ্যন্তরে"
    ],
    "examPointsEn": [
      "Asia’s only natural carp breeding ground for Rui, Katla & Mrigel",
      "Declared a National Fisheries Heritage river in 2020",
      "Originates and ends entirely inside Bangladesh"
    ],
    "coords": [
      [
        91.77,
        22.92
      ],
      [
        91.82,
        22.68
      ],
      [
        91.87,
        22.46
      ],
      [
        91.89,
        22.39
      ]
    ]
  },
  {
    "id": "buriganga",
    "nameBn": "বুড়িগঙ্গা নদী",
    "nameEn": "Buriganga River",
    "lengthKm": "২৭ কি.মি.",
    "lengthKmEn": "27 km",
    "originBn": "ধলেশ্বরী নদীর শাখানদী",
    "originEn": "Distributary of the Dhaleshwari River",
    "mouthBn": "নারায়ণগঞ্জের ফতুল্লায় পুনরায় ধলেশ্বরীতে",
    "mouthEn": "Rejoins the Dhaleshwari near Fatullah",
    "districtsBn": "ঢাকা, নারায়ণগঞ্জ",
    "districtsEn": "Dhaka, Narayanganj",
    "tributariesBn": "তুরাগ নদী",
    "tributariesEn": "Turag River",
    "summaryBn": "রাজধানী ঢাকা বুড়িগঙ্গা নদীর তীরে গড়ে উঠেছে। ঐতিহাসিক সদরঘাট ও আহসান মঞ্জিল এই নদীর তীরে অবস্থিত।",
    "summaryEn": "Historic river along which Dhaka developed; home to Sadarghat launch terminal and Ahsan Manzil.",
    "examPointsBn": [
      "ঢাকা শহর বুড়িগঙ্গা নদীর তীরে অবস্থিত (সদরঘাট, আহসান মঞ্জিল, লালবাগ)",
      "বুড়িগঙ্গা মূলত ধলেশ্বরী নদীর শাখানদী",
      "হাইকোর্ট কর্তৃক নদীকে “জীবন্ত সত্তা” (Living Entity) ঘোষণার অন্যতম আলোচিত নদী"
    ],
    "examPointsEn": [
      "Capital Dhaka grew along the Buriganga (Sadarghat, Ahsan Manzil)",
      "Distributary of the Dhaleshwari River",
      "Central to Bangladesh’s landmark \"Rivers as Living Entities\" High Court ruling"
    ],
    "coords": [
      [
        90.34,
        23.75
      ],
      [
        90.39,
        23.71
      ],
      [
        90.44,
        23.66
      ],
      [
        90.48,
        23.62
      ]
    ]
  },
  {
    "id": "gorai-madhumati",
    "nameBn": "গড়াই-মধুমতী নদী",
    "nameEn": "Gorai-Madhumati River",
    "lengthKm": "৩৭০ কি.মি.",
    "lengthKmEn": "370 km",
    "originBn": "কুষ্টিয়ার তালবাড়িয়ায় পদ্মা থেকে উৎপন্ন",
    "originEn": "Branches off Padma at Talbaria (Kushtia)",
    "mouthBn": "বলেশ্বর নামে বঙ্গোপসাগরে পতিত",
    "mouthEn": "Bay of Bengal as the Baleshwar River",
    "districtsBn": "কুষ্টিয়া, রাজবাড়ী, ঝিনাইদহ, মাগুরা, ফরিদপুর, নড়াইল, গোপালগঞ্জ, বাগেরহাট, পিরোজপুর",
    "districtsEn": "Kushtia, Rajbari, Jhenaidah, Magura, Faridpur, Narail, Gopalganj, Bagerhat, Pirojpur",
    "tributariesBn": "পদ্মার প্রধান শাখানদী",
    "tributariesEn": "Major distributary of the Padma",
    "summaryBn": "দক্ষিণ-পশ্চিমাঞ্চল ও সুন্দরবনে মিঠাপানি সরবরাহের প্রধান উৎস। উজানে গড়াই, মধ্যভাগে মধুমতী ও ভাটিতে বলেশ্বর নামে পরিচিত।",
    "summaryEn": "Key freshwater lifeline for southwestern Bangladesh and the Sundarbans; known sequentially as Gorai, Madhumati, and Baleshwar.",
    "examPointsBn": [
      "পদ্মার প্রধান শাখানদী—উজানে গড়াই, মাঝে মধুমতী ও ভাটিতে বলেশ্বর নামে পরিচিত",
      "মধুমতী সেতু (কালনা সেতু—বাংলাদেশের প্রথম ৬ লেন সেতু) মধুমতী নদীর ওপর নির্মিত"
    ],
    "examPointsEn": [
      "Primary distributary of Padma; named Gorai → Madhumati → Baleshwar",
      "Kalna (Madhumati) Bridge — Bangladesh’s first 6-lane bridge spans it"
    ],
    "coords": [
      [
        89.1,
        23.95
      ],
      [
        89.38,
        23.72
      ],
      [
        89.62,
        23.4
      ],
      [
        89.78,
        23.02
      ],
      [
        89.88,
        22.58
      ],
      [
        89.9,
        22.12
      ]
    ]
  },
  {
    "id": "naf",
    "nameBn": "নাফ নদী",
    "nameEn": "Naf River",
    "lengthKm": "৬৩ কি.মি.",
    "lengthKmEn": "63 km",
    "originBn": "মিয়ানমারের আরাকান পাহাড়",
    "originEn": "Arakan Hills of Myanmar",
    "mouthBn": "টেকনাফ হয়ে বঙ্গোপসাগরে",
    "mouthEn": "Bay of Bengal at Teknaf",
    "districtsBn": "কক্সবাজার (টেকনাফ)",
    "districtsEn": "Cox’s Bazar (Teknaf)",
    "tributariesBn": "বাংলাদেশ-মিয়ানমার সীমান্ত নদী",
    "tributariesEn": "Bangladesh-Myanmar border river",
    "summaryBn": "বাংলাদেশের দক্ষিণ-পূর্ব সীমান্তে বাংলাদেশ ও মিয়ানমারকে বিভক্তকারী কৌশলগত সীমান্ত নদী।",
    "summaryEn": "Strategic transboundary river separating southeastern Bangladesh (Teknaf) from Myanmar.",
    "examPointsBn": [
      "বাংলাদেশ ও মিয়ানমারকে বিভক্তকারী প্রধান সীমান্ত নদী—নাফ নদী",
      "নাফ নদীর মোহনায় শাহপরীর দ্বীপ ও দক্ষিণে সেন্ট মার্টিন দ্বীপ অবস্থিত"
    ],
    "examPointsEn": [
      "Forms the international border between Bangladesh and Myanmar",
      "Shah Porir Dwip and St. Martin’s Island lie near its estuary"
    ],
    "coords": [
      [
        92.22,
        21.18
      ],
      [
        92.27,
        21
      ],
      [
        92.31,
        20.86
      ],
      [
        92.34,
        20.73
      ]
    ]
  },
  {
    "id": "sangu",
    "nameBn": "সাঙ্গু নদী",
    "nameEn": "Sangu River",
    "lengthKm": "১৭৩ কি.মি.",
    "lengthKmEn": "173 km",
    "originBn": "বাংলাদেশ-মিয়ানমার সীমান্তের উত্তর আরাকান পাহাড় (থানচি, বান্দরবান)",
    "originEn": "North Arakan Hills near Thanchi (Bandarban)",
    "mouthBn": "চট্টগ্রামের বাঁশখালী-আনোয়ারা হয়ে বঙ্গোপসাগরে",
    "mouthEn": "Bay of Bengal via Banshkhali-Anwara (Chattogram)",
    "districtsBn": "বান্দরবান, চট্টগ্রাম",
    "districtsEn": "Bandarban, Chattogram",
    "tributariesBn": "সাঙ্গু বাংলাদেশের একমাত্র সমুদ্রবক্ষ গ্যাসক্ষেত্রের নামকরণ উৎস",
    "tributariesEn": "Namesake of Bangladesh’s first offshore gas field (Sangu)",
    "summaryBn": "বান্দরবান শহরের পাশ দিয়ে বয়ে যাওয়া পাহাড়ি খরস্রোতা নদী।",
    "summaryEn": "Scenic mountain river flowing through Bandarban town into the Bay of Bengal.",
    "examPointsBn": [
      "উৎপত্তি ও সমাপ্তি বাংলাদেশের ভূখণ্ডেই (বান্দরবানের প্রধান নদী)",
      "সাঙ্গু গ্যাসক্ষেত্র—বাংলাদেশের প্রথম ও একমাত্র উৎপাদিত সমুদ্রবক্ষ গ্যাসক্ষেত্র"
    ],
    "examPointsEn": [
      "Flows through Bandarban and empties into the Bay of Bengal",
      "Namesake of Sangu Gas Field — Bangladesh’s first offshore gas field"
    ],
    "coords": [
      [
        92.54,
        21.62
      ],
      [
        92.4,
        21.92
      ],
      [
        92.22,
        22.19
      ],
      [
        91.95,
        22.14
      ],
      [
        91.84,
        22.12
      ]
    ]
  },
  {
    "id": "rupsha-pasur",
    "nameBn": "রূপসা-পশুর নদী",
    "nameEn": "Rupsha-Pasur River",
    "lengthKm": "১৪২ কি.মি.",
    "lengthKmEn": "142 km",
    "originBn": "ভৈরব ও আতাই নদীর মিলিত ধারা (খুলনা)",
    "originEn": "Confluence of Bhairab and Atai rivers at Khulna",
    "mouthBn": "সুন্দরবন হয়ে বঙ্গোপসাগরে",
    "mouthEn": "Bay of Bengal through the Sundarbans",
    "districtsBn": "খুলনা, বাগেরহাট",
    "districtsEn": "Khulna, Bagerhat",
    "tributariesBn": "কাজীবাছা ও শিবসা নদী",
    "tributariesEn": "Kazibacha & Shibsa rivers",
    "summaryBn": "খুলনা ও মোংলা সমুদ্রবন্দরের প্রধান নৌপথ। সুন্দরবনের ভেতর দিয়ে প্রবাহিত হয়ে বঙ্গোপসাগরে পড়েছে।",
    "summaryEn": "Primary navigation channel serving Khulna and Mongla Seaport through the Sundarbans.",
    "examPointsBn": [
      "মোংলা সমুদ্রবন্দর পশুর নদীর তীরে অবস্থিত",
      "খুলনা শহর ও খানজাহান আলী সেতু (রূপসা সেতু) এই নদীর তীরে"
    ],
    "examPointsEn": [
      "Mongla Seaport is located on the banks of the Pasur River",
      "Khulna city & Rupsha Bridge sit along its upper stretch"
    ],
    "coords": [
      [
        89.57,
        22.82
      ],
      [
        89.56,
        22.64
      ],
      [
        89.59,
        22.46
      ],
      [
        89.57,
        22.15
      ],
      [
        89.54,
        21.85
      ]
    ]
  },
  {
    "id": "karatoya-atrai",
    "nameBn": "করতোয়া-আত্রাই নদী",
    "nameEn": "Karatoya–Atrai River",
    "lengthKm": "৫২৫ কি.মি. (সম্মিলিত)",
    "lengthKmEn": "525 km (combined)",
    "originBn": "ভারতের জলপাইগুড়ি ও দার্জিলিং অঞ্চল",
    "originEn": "Jalpaiguri & Darjeeling foothills, India",
    "mouthBn": "চলনবিল ও হুড়াসাগর হয়ে যমুনায়",
    "mouthEn": "Jamuna River via Chalan Beel & Hurasagar",
    "districtsBn": "পঞ্চগড়, দিনাজপুর, রংপুর, বগুড়া, নওগাঁ, নাটোর, পাবনা",
    "districtsEn": "Panchagarh, Dinajpur, Rangpur, Bogura, Naogaon, Natore, Pabna",
    "tributariesBn": "মহাস্থানগড় করতোয়া নদীর তীরে অবস্থিত",
    "tributariesEn": "Ancient Mahasthangarh sits on the banks of Karatoya",
    "summaryBn": "উত্তরবঙ্গের দীর্ঘতম ঐতিহাসিক নদী প্রণালি। প্রাচীন পুণ্ড্রনগর (মহাস্থানগড়) করতোয়া নদীর তীরে গড়ে উঠেছিল।",
    "summaryEn": "Longest historic river system of northern Bangladesh; ancient Mahasthangarh flourished on the Karatoya.",
    "examPointsBn": [
      "মহাস্থানগড় (পুণ্ড্রনগর) করতোয়া নদীর তীরে অবস্থিত",
      "আত্রাই নদী চলনবিলের প্রধান প্রবাহ পথ",
      "উত্তরবঙ্গের কৃষি ও বরেন্দ্র অঞ্চলের প্রাণরেখা"
    ],
    "examPointsEn": [
      "Mahasthangarh (Pundranagara) stands on the Karatoya River",
      "Atrai River is the main channel feeding Chalan Beel",
      "Lifeline of the northern Barind agricultural region"
    ],
    "coords": [
      [
        88.55,
        26.35
      ],
      [
        88.78,
        25.65
      ],
      [
        89.18,
        25.25
      ],
      [
        89.36,
        24.85
      ],
      [
        89.42,
        24.45
      ],
      [
        89.62,
        24.08
      ]
    ]
  },
  {
    "id": "kopotakkho",
    "nameBn": "কপোতাক্ষ নদ",
    "nameEn": "Kopotakkho River",
    "lengthKm": "১৮২ কি.মি.",
    "lengthKmEn": "182 km",
    "originBn": "চুয়াডাঙ্গা-যশোর অঞ্চলের ভৈরব নদ থেকে",
    "originEn": "Offtake from Bhairab River in Chuadanga–Jashore",
    "mouthBn": "খুলনার কয়রায় খোলপেটুয়া-শিবসা হয়ে বঙ্গোপসাগরে",
    "mouthEn": "Kholpetua–Shibsa estuary in Khulna",
    "districtsBn": "চুয়াডাঙ্গা, ঝিনাইদহ, যশোর, সাতক্ষীরা, খুলনা",
    "districtsEn": "Chuadanga, Jhenaidah, Jashore, Satkhira, Khulna",
    "tributariesBn": "বাংলা সাহিত্যের বিখ্যাত \"কপোতাক্ষ নদ\" সনেটের নদী",
    "tributariesEn": "Immortalized by Michael Madhusudan Dutt’s sonnet",
    "summaryBn": "মহাকবি মাইকেল মধুসূদন দত্তের জন্মস্থান সাগরদাঁড়ি (কেশবপুর, যশোর) এই নদের তীরে অবস্থিত।",
    "summaryEn": "Flows past Sagardari (Jashore), birthplace of poet Michael Madhusudan Dutt.",
    "examPointsBn": [
      "মাইকেল মধুসূদন দত্তের \"কপোতাক্ষ নদ\" চতুর্দশপদী কবিতা (সনেট) এই নদ নিয়ে রচিত",
      "যশোরের কেশবপুর উপজেলার সাগরদাঁড়ি কপোতাক্ষের তীরে অবস্থিত",
      "কপোতাক্ষ একটি নদ (বাংলাদেশ-ভারত সীমান্ত অঞ্চলের প্রবাহ)"
    ],
    "examPointsEn": [
      "Subject of Michael Madhusudan Dutt’s famous Bengali sonnet",
      "Sagardari (Keshabpur, Jashore) stands on its bank",
      "Flows across Jashore, Satkhira & Khulna into the Sundarbans"
    ],
    "coords": [
      [
        88.95,
        23.52
      ],
      [
        89.08,
        23.18
      ],
      [
        89.18,
        22.88
      ],
      [
        89.24,
        22.55
      ],
      [
        89.3,
        22.25
      ]
    ]
  },
  {
    "id": "bangshi",
    "nameBn": "বংশী নদী",
    "nameEn": "Bangshi River",
    "lengthKm": "১৮৫ কি.মি.",
    "lengthKmEn": "185 km",
    "originBn": "জামালপুরের পুরাতন ব্রহ্মপুত্র থেকে উৎপন্ন",
    "originEn": "Distributary of Old Brahmaputra in Jamalpur",
    "mouthBn": "সাভারে ধলেশ্বরী নদীতে মিলিত",
    "mouthEn": "Joins Dhaleshwari River at Savar, Dhaka",
    "districtsBn": "জামালপুর, টাঙ্গাইল, গাজীপুর, ঢাকা (সাভার)",
    "districtsEn": "Jamalpur, Tangail, Gazipur, Dhaka (Savar)",
    "tributariesBn": "মধুপুর গড় ও সাভার অঞ্চলের প্রধান নদী",
    "tributariesEn": "Flows along Madhupur Tract and Savar",
    "summaryBn": "জামালপুর থেকে মধুপুর ও টাঙ্গাইল হয়ে ঢাকার সাভারে ধলেশ্বরীতে মিশেছে। জাতীয় স্মৃতিসৌধের অদূরে প্রবাহিত।",
    "summaryEn": "Flows south from Jamalpur through Madhupur & Tangail to join Dhaleshwari at Savar.",
    "examPointsBn": [
      "সাভার ও ধামরাই বংশী নদীর তীরে অবস্থিত",
      "পুরাতন ব্রহ্মপুত্রের শাখানদী এবং ধলেশ্বরীর উপনদী"
    ],
    "examPointsEn": [
      "Savar & Dhamrai lie along the Bangshi River",
      "Distributary of Old Brahmaputra and tributary of Dhaleshwari"
    ],
    "coords": [
      [
        89.95,
        24.85
      ],
      [
        90.02,
        24.55
      ],
      [
        90.12,
        24.22
      ],
      [
        90.24,
        23.85
      ]
    ]
  },
  {
    "id": "ghaghat",
    "nameBn": "ঘাঘট নদী",
    "nameEn": "Ghaghat River",
    "lengthKm": "২৩৮ কি.মি.",
    "lengthKmEn": "238 km",
    "originBn": "নীলফামারীর কিশোরগঞ্জ অঞ্চল (তিস্তার শাখা)",
    "originEn": "Nilphamari (distributary of Teesta)",
    "mouthBn": "গাইবান্ধায় করতোয়া-যমুনায় মিলিত",
    "mouthEn": "Joins Karatoya/Jamuna in Gaibandha",
    "districtsBn": "নীলফামারী, রংপুর, গাইবান্ধা",
    "districtsEn": "Nilphamari, Rangpur, Gaibandha",
    "tributariesBn": "রংপুর ও গাইবান্ধা শহর ঘাঘট নদীর তীরে",
    "tributariesEn": "Flows through Rangpur & Gaibandha towns",
    "summaryBn": "রংপুর ও গাইবান্ধা জেলার প্রধান অভ্যন্তরীণ নদী, যা তিস্তা-ব্রহ্মপুত্র অববাহিকার অংশ।",
    "summaryEn": "Important internal river of Rangpur and Gaibandha within the Teesta–Jamuna basin.",
    "examPointsBn": [
      "রংপুর ও গাইবান্ধা শহর ঘাঘট নদীর তীরে অবস্থিত",
      "উত্তরবঙ্গের ২৩৮ কি.মি. দীর্ঘ নদী (উইকিপিডিয়া)"
    ],
    "examPointsEn": [
      "Rangpur and Gaibandha towns stand on the Ghaghat River",
      "238 km long river in northern Bangladesh"
    ],
    "coords": [
      [
        89.05,
        25.88
      ],
      [
        89.24,
        25.72
      ],
      [
        89.42,
        25.45
      ],
      [
        89.55,
        25.25
      ]
    ]
  },
  {
    "id": "nabaganga",
    "nameBn": "নবগঙ্গা নদী",
    "nameEn": "Nabaganga River",
    "lengthKm": "২৩২ কি.মি.",
    "lengthKmEn": "232 km",
    "originBn": "চুয়াডাঙ্গায় মাথাভাঙ্গা নদী থেকে উৎপন্ন",
    "originEn": "Offtake from Mathabhanga River in Chuadanga",
    "mouthBn": "নড়াইলের বড়দিয়ায় মধুমতি নদীতে মিলিত",
    "mouthEn": "Joins Madhumati River at Narail",
    "districtsBn": "চুয়াডাঙ্গা, ঝিনাইদহ, মাগুরা, নড়াইল",
    "districtsEn": "Chuadanga, Jhenaidah, Magura, Narail",
    "tributariesBn": "মাগুরা ও ঝিনাইদহ শহর নবগঙ্গা নদীর তীরে",
    "tributariesEn": "Jhenaidah and Magura towns lie on its bank",
    "summaryBn": "দক্ষিণ-পশ্চিমাঞ্চলের অন্যতম প্রধান নদী, যা ঝিনাইদহ ও মাগুরা শহরের বুক চিরে মধুমতিতে মিশেছে।",
    "summaryEn": "Key southwestern river flowing through Jhenaidah and Magura into the Madhumati.",
    "examPointsBn": [
      "ঝিনাইদহ ও মাগুরা শহর নবগঙ্গা নদীর তীরে অবস্থিত",
      "মাথাভাঙ্গা নদী থেকে উৎপন্ন হয়ে মধুমতিতে পড়েছে"
    ],
    "examPointsEn": [
      "Jhenaidah and Magura towns sit on the Nabaganga",
      "Originates from Mathabhanga and joins Madhumati"
    ],
    "coords": [
      [
        88.85,
        23.62
      ],
      [
        89.18,
        23.54
      ],
      [
        89.42,
        23.48
      ],
      [
        89.62,
        23.15
      ]
    ]
  },
  {
    "id": "kangsa",
    "nameBn": "কংস (ভোগাই-কংস) নদী",
    "nameEn": "Kangsa (Bhogai–Kangsa) River",
    "lengthKm": "২২৭ কি.মি.",
    "lengthKmEn": "227 km",
    "originBn": "ভারতের মেঘালয়ের গারো পাহাড়",
    "originEn": "Garo Hills of Meghalaya, India",
    "mouthBn": "সুনামগঞ্জ-কিশোরগঞ্জের ধনু/বৌলাই নদীতে",
    "mouthEn": "Dhanu–Baulai river system in Haor basin",
    "districtsBn": "শেরপুর, ময়মনসিংহ, নেত্রকোনা, সুনামগঞ্জ",
    "districtsEn": "Sherpur, Mymensingh, Netrokona, Sunamganj",
    "tributariesBn": "উপনদী: সোমেশ্বরী, নিতাই",
    "tributariesEn": "Tributaries: Someshwari, Nitai",
    "summaryBn": "গারো পাহাড় থেকে নেমে আসা সীমান্তবর্তী পাহাড়ি নদী যা নেত্রকোনা ও হাওর অঞ্চলের প্রধান প্রবাহ।",
    "summaryEn": "Transboundary river descending from the Garo Hills through Sherpur & Netrokona into the Haor basin.",
    "examPointsBn": [
      "নেত্রকোনা শহর কংস (মগড়া) অববাহিকায় অবস্থিত",
      "উৎপত্তি: ভারতের মেঘালয়ের গারো পাহাড়"
    ],
    "examPointsEn": [
      "Major river of Netrokona and the northern Haor basin",
      "Originates in the Garo Hills of Meghalaya, India"
    ],
    "coords": [
      [
        90.15,
        25.18
      ],
      [
        90.45,
        25.05
      ],
      [
        90.75,
        24.92
      ],
      [
        91.05,
        24.82
      ]
    ]
  },
  {
    "id": "dakatia",
    "nameBn": "ডাকাতিয়া নদী",
    "nameEn": "Dakatia River",
    "lengthKm": "২০৭ কি.মি.",
    "lengthKmEn": "207 km",
    "originBn": "ভারতের ত্রিপুরা রাজ্যের পাহাড়",
    "originEn": "Tripura Hills, India",
    "mouthBn": "চাঁদপুরে মেঘনা নদীতে মিলিত",
    "mouthEn": "Meghna River at Chandpur",
    "districtsBn": "কুমিল্লা, চাঁদপুর, লক্ষ্মীপুর",
    "districtsEn": "Cumilla, Chandpur, Lakshmipur",
    "tributariesBn": "চাঁদপুর শহর পদ্মা-মেঘনা-ডাকাতিয়ার মোহনায়",
    "tributariesEn": "Meets Padma & Meghna at Chandpur",
    "summaryBn": "ত্রিপুরা থেকে কুমিল্লা হয়ে চাঁদপুরে মেঘনায় পতিত দক্ষিণ-পূর্বাঞ্চলের গুরুত্বপূর্ণ নদী।",
    "summaryEn": "Transboundary river flowing from Tripura through Cumilla to join Meghna at Chandpur.",
    "examPointsBn": [
      "চাঁদপুর শহর পদ্মা, মেঘনা ও ডাকাতিয়া নদীর সঙ্গমস্থলে অবস্থিত",
      "ভারতের ত্রিপুরা পাহাড় থেকে উৎপন্ন"
    ],
    "examPointsEn": [
      "Chandpur town stands at the confluence of Padma, Meghna & Dakatia",
      "Originates in the Tripura hills of India"
    ],
    "coords": [
      [
        91.32,
        23.28
      ],
      [
        91.1,
        23.22
      ],
      [
        90.85,
        23.22
      ],
      [
        90.65,
        23.23
      ]
    ]
  },
  {
    "id": "feni",
    "nameBn": "ফেনী নদী",
    "nameEn": "Feni River",
    "lengthKm": "১১৬ কি.মি.",
    "lengthKmEn": "116 km",
    "originBn": "ভারতের ত্রিপুরা রাজ্য (সাব্রুম সংলগ্ন)",
    "originEn": "Tripura, India",
    "mouthBn": "সন্দ্বীপ চ্যানেলে বঙ্গোপসাগরে",
    "mouthEn": "Bay of Bengal (Sandwip Channel)",
    "districtsBn": "খাগড়াছড়ি, ফেনী, চট্টগ্রাম (মীরসরাই)",
    "districtsEn": "Khagrachhari, Feni, Chattogram (Mirsarai)",
    "tributariesBn": "মুহুরী ও ছোট ফেনী নদী সংলগ্ন অববাহিকা",
    "tributariesEn": "Muhuri & Little Feni basin",
    "summaryBn": "বাংলাদেশ-ভারত সীমান্ত নদী। রামগড়-সাব্রুম মৈত্রী সেতু-১ ফেনী নদীর ওপর নির্মিত।",
    "summaryEn": "Transboundary river forming part of the Bangladesh–India border; site of Maitri Setu-1.",
    "examPointsBn": [
      "বাংলাদেশ-ভারত মৈত্রী সেতু-১ (রামগড়–সাব্রুম) ফেনী নদীর ওপর নির্মিত",
      "ফেনী নদীর ১.৮২ কিউসেক পানি ত্রিপুরার সাব্রুম শহরে সরবরাহের চুক্তি (২০১৯)",
      "মুহুরী সেচ প্রকল্প ও দেশের প্রথম বায়ুবিদ্যুৎ কেন্দ্র এই অববাহিকায়"
    ],
    "examPointsEn": [
      "Bangladesh–India Maitri Setu-1 (Ramgarh–Sabroom) crosses the Feni River",
      "2019 MoU allows India to withdraw 1.82 cusecs for Sabroom",
      "Site of Muhuri Irrigation Project & first wind power plant"
    ],
    "coords": [
      [
        91.75,
        23.02
      ],
      [
        91.55,
        22.92
      ],
      [
        91.42,
        22.82
      ],
      [
        91.4,
        22.72
      ]
    ]
  },
  {
    "id": "matamuhuri",
    "nameBn": "মাতামুহুরী নদী",
    "nameEn": "Matamuhuri River",
    "lengthKm": "২৮৭ কি.মি.",
    "lengthKmEn": "287 km",
    "originBn": "বান্দরবানের আলীকদমের লামা পাহাড়",
    "originEn": "Lama / Alikadam Hills in Bandarban",
    "mouthBn": "চকরিয়া হয়ে মহেশখালী চ্যানেলে বঙ্গোপসাগরে",
    "mouthEn": "Bay of Bengal via Chakaria & Moheshkhali Channel",
    "districtsBn": "বান্দরবান, কক্সবাজার",
    "districtsEn": "Bandarban, Cox’s Bazar",
    "tributariesBn": "উৎপত্তি ও সমাপ্তি সম্পূর্ণ বাংলাদেশের অভ্যন্তরে",
    "tributariesEn": "Originates and terminates entirely within Bangladesh",
    "summaryBn": "বান্দরবানের আলীকদম থেকে উৎপন্ন হয়ে কক্সবাজারের চকরিয়া দিয়ে বঙ্গোপসাগরে পতিত সম্পূর্ণ দেশীয় নদী।",
    "summaryEn": "River originating and ending entirely inside Bangladesh, flowing through Alikadam, Lama & Chakaria.",
    "examPointsBn": [
      "মাতামুহুরী ও হালদা—উৎপত্তি ও পতন উভয়ই বাংলাদেশের অভ্যন্তরে",
      "উৎপত্তিস্থল: বান্দরবানের আলীকদম পাহাড়; মোহনা: বঙ্গোপসাগর (চকরিয়া)"
    ],
    "examPointsEn": [
      "Both originates and terminates entirely within Bangladesh",
      "Flows through Alikadam, Lama (Bandarban) and Chakaria (Cox’s Bazar)"
    ],
    "coords": [
      [
        92.35,
        21.62
      ],
      [
        92.18,
        21.75
      ],
      [
        92.08,
        21.77
      ],
      [
        91.92,
        21.72
      ]
    ]
  },
  {
    "id": "chitra",
    "nameBn": "চিত্রা নদী",
    "nameEn": "Chitra River",
    "lengthKm": "১৭০ কি.মি.",
    "lengthKmEn": "170 km",
    "originBn": "চুয়াডাঙ্গা-ঝিনাইদহ বিল অঞ্চল",
    "originEn": "Beels of Chuadanga & Jhenaidah",
    "mouthBn": "নড়াইলের গাজীপুরে নবগঙ্গা নদীতে মিলিত",
    "mouthEn": "Nabaganga River in Narail",
    "districtsBn": "চুয়াডাঙ্গা, ঝিনাইদহ, মাগুরা, নড়াইল",
    "districtsEn": "Chuadanga, Jhenaidah, Magura, Narail",
    "tributariesBn": "নড়াইল শহর ও শিল্পী এস এম সুলতানের স্মৃতিবিজড়িত নদী",
    "tributariesEn": "Narail town & artist SM Sultan’s riverside heritage",
    "summaryBn": "নড়াইল শহরের প্রাণকেন্দ্র দিয়ে প্রবাহিত শান্ত নদী, বিশ্ববরেণ্য চিত্রশিল্পী এস এম সুলতানের স্মৃতিবিজড়িত।",
    "summaryEn": "Tranquil river flowing through Narail town, closely associated with legendary painter SM Sultan.",
    "examPointsBn": [
      "নড়াইল জেলা শহর চিত্রা নদীর তীরে অবস্থিত",
      "চিত্রশিল্পী এস এম সুলতানের বাড়ি ও নৌকা জাদুঘর চিত্রা নদীর তীরে"
    ],
    "examPointsEn": [
      "Narail district town stands on the Chitra River",
      "Home to painter SM Sultan’s memorial complex & boat"
    ],
    "coords": [
      [
        89.02,
        23.58
      ],
      [
        89.25,
        23.38
      ],
      [
        89.48,
        23.18
      ],
      [
        89.56,
        23.08
      ]
    ]
  },
  {
    "id": "punarbhaba",
    "nameBn": "পুনর্ভবা নদী",
    "nameEn": "Punarbhaba River",
    "lengthKm": "১৬০ কি.মি.",
    "lengthKmEn": "160 km",
    "originBn": "ঠাকুরগাঁও-দিনাজপুরের নিম্নভূমি",
    "originEn": "Lowlands of Thakurgaon & Dinajpur",
    "mouthBn": "চাঁপাইনবাবগঞ্জের রোহনপুরে মহানন্দা নদীতে",
    "mouthEn": "Mahananda River at Rohanpur, Chapainawabganj",
    "districtsBn": "ঠাকুরগাঁও, দিনাজপুর, নওগাঁ, চাঁপাইনবাবগঞ্জ",
    "districtsEn": "Thakurgaon, Dinajpur, Naogaon, Chapainawabganj",
    "tributariesBn": "দিনাজপুর শহর পুনর্ভবা নদীর তীরে অবস্থিত",
    "tributariesEn": "Dinajpur town sits on the Punarbhaba River",
    "summaryBn": "উত্তর-পশ্চিমাঞ্চলের অন্যতম প্রধান নদী, যা দিনাজপুর শহরের পাশ দিয়ে প্রবাহিত হয়ে মহানন্দায় মিশেছে।",
    "summaryEn": "Major northwestern river passing Dinajpur town before joining the Mahananda River.",
    "examPointsBn": [
      "দিনাজপুর জেলা শহর পুনর্ভবা নদীর তীরে অবস্থিত",
      "চাঁপাইনবাবগঞ্জের রোহনপুরে মহানন্দা নদীর সাথে মিলিত হয়েছে"
    ],
    "examPointsEn": [
      "Dinajpur town lies on the banks of the Punarbhaba",
      "Joins the Mahananda River at Rohanpur, Chapainawabganj"
    ],
    "coords": [
      [
        88.58,
        25.88
      ],
      [
        88.62,
        25.62
      ],
      [
        88.54,
        25.15
      ],
      [
        88.32,
        24.82
      ]
    ]
  },
  {
    "id": "arial-khan",
    "nameBn": "আড়িয়াল খাঁ নদী",
    "nameEn": "Arial Khan River",
    "lengthKm": "১৬৪ কি.মি.",
    "lengthKmEn": "164 km",
    "originBn": "ফরিদপুর-মাদারীপুরে পদ্মা নদী থেকে উৎপন্ন",
    "originEn": "Distributary of Padma in Faridpur–Madaripur",
    "mouthBn": "বরিশালে মেঘনা/তেঁতুলিয়া মোহনায়",
    "mouthEn": "Meghna / Barishal estuary",
    "districtsBn": "ফরিদপুর, মাদারীপুর, শরীয়তপুর, বরিশাল",
    "districtsEn": "Faridpur, Madaripur, Shariatpur, Barishal",
    "tributariesBn": "মাদারীপুর শহর আড়িয়াল খাঁ নদীর তীরে অবস্থিত",
    "tributariesEn": "Madaripur town sits on the Arial Khan River",
    "summaryBn": "পদ্মার প্রধান দক্ষিণাঞ্চলীয় শাখানদী, যা মাদারীপুর ও বরিশাল অঞ্চলের প্রধান জলপথ।",
    "summaryEn": "Major southern distributary of the Padma flowing through Madaripur into Barishal.",
    "examPointsBn": [
      "মাদারীপুর শহর আড়িয়াল খাঁ নদীর তীরে অবস্থিত",
      "পদ্মা নদীর অন্যতম প্রধান শাখানদী"
    ],
    "examPointsEn": [
      "Madaripur town stands on the Arial Khan River",
      "Principal southern distributary of the Padma"
    ],
    "coords": [
      [
        90.08,
        23.45
      ],
      [
        90.18,
        23.18
      ],
      [
        90.28,
        22.92
      ],
      [
        90.42,
        22.72
      ]
    ]
  },
  {
    "id": "dhaleshwari",
    "nameBn": "ধলেশ্বরী নদী",
    "nameEn": "Dhaleshwari River",
    "lengthKm": "১৬৯ কি.মি.",
    "lengthKmEn": "169 km",
    "originBn": "টাঙ্গাইলে যমুনা নদী থেকে উৎপন্ন",
    "originEn": "Distributary of Jamuna River in Tangail",
    "mouthBn": "মুন্সীগঞ্জে মেঘনা নদীতে মিলিত",
    "mouthEn": "Joins Meghna River at Munshiganj",
    "districtsBn": "টাঙ্গাইল, মানিকগঞ্জ, ঢাকা, নারায়ণগঞ্জ, মুন্সীগঞ্জ",
    "districtsEn": "Tangail, Manikganj, Dhaka, Narayanganj, Munshiganj",
    "tributariesBn": "শাখানদী: বুড়িগঙ্গা; উপনদী: বংশী, তুরাগ",
    "tributariesEn": "Distributary: Buriganga; Tributaries: Bangshi, Turag",
    "summaryBn": "যমুনার প্রধান শাখানদী। বুড়িগঙ্গা ধলেশ্বরীরই শাখা। মানিকগঞ্জ ও মুন্সীগঞ্জের প্রধান নদী।",
    "summaryEn": "Major distributary of the Jamuna; parent river of the Buriganga, flowing through Manikganj & Munshiganj.",
    "examPointsBn": [
      "বুড়িগঙ্গা নদী ধলেশ্বরী নদীর শাখানদী",
      "মুন্সীগঞ্জ জেলা শহর ধলেশ্বরী-মেঘনার সঙ্গমস্থলে অবস্থিত",
      "যমুনা থেকে উৎপন্ন হয়ে মেঘনায় মিলিত হয়েছে"
    ],
    "examPointsEn": [
      "Buriganga River is a distributary of the Dhaleshwari",
      "Munshiganj lies at the Dhaleshwari–Meghna confluence",
      "Connects the Jamuna system to the Meghna system"
    ],
    "coords": [
      [
        89.82,
        24.18
      ],
      [
        89.98,
        23.85
      ],
      [
        90.28,
        23.62
      ],
      [
        90.55,
        23.54
      ]
    ]
  },
  {
    "id": "bhairab",
    "nameBn": "ভৈরব নদ",
    "nameEn": "Bhairab River",
    "lengthKm": "২১৯ কি.মি.",
    "lengthKmEn": "219 km",
    "originBn": "ভারতের পশ্চিমবঙ্গের জলঙ্গী থেকে উৎপন্ন",
    "originEn": "Jalangi River in West Bengal, India",
    "mouthBn": "খুলনায় রূপসা নদীতে মিলিত",
    "mouthEn": "Joins Rupsa River at Khulna",
    "districtsBn": "মেহেরপুর, চুয়াডাঙ্গা, যশোর, খুলনা",
    "districtsEn": "Meherpur, Chuadanga, Jashore, Khulna",
    "tributariesBn": "যশোর ও খুলনা শহর ভৈরব নদের তীরে",
    "tributariesEn": "Jashore and Khulna sit along the Bhairab",
    "summaryBn": "দক্ষিণ-পশ্চিমাঞ্চলের প্রাচীনতম নদগুলোর একটি। যশোর শহর ও খুলনার খালিশপুর শিল্পাঞ্চল এর তীরে অবস্থিত।",
    "summaryEn": "One of the oldest rivers of southwestern Bangladesh; flows past Jashore town into Khulna.",
    "examPointsBn": [
      "যশোর শহর ভৈরব নদের তীরে অবস্থিত",
      "মেহেরপুর, চুয়াডাঙ্গা ও যশোর হয়ে খুলনায় রূপসা নদীতে মিশেছে"
    ],
    "examPointsEn": [
      "Jashore town stands on the banks of the Bhairab River",
      "Flows through Meherpur, Chuadanga & Jashore to join Rupsa at Khulna"
    ],
    "coords": [
      [
        88.62,
        23.78
      ],
      [
        88.92,
        23.45
      ],
      [
        89.22,
        23.17
      ],
      [
        89.55,
        22.85
      ]
    ]
  },
  {
    "id": "mathabhanga",
    "nameBn": "মাথাভাঙ্গা নদী",
    "nameEn": "Mathabhanga River",
    "lengthKm": "১৩০ কি.মি.",
    "lengthKmEn": "130 km",
    "originBn": "ভারতের মুর্শিদাবাদে পদ্মা (গঙ্গা) থেকে উৎপন্ন",
    "originEn": "Offtake from Padma (Ganges) in Murshidabad, India",
    "mouthBn": "কুষ্টিয়া-চুয়াডাঙ্গা হয়ে ইছামতী ও চূর্ণীতে বিভক্ত",
    "mouthEn": "Bifurcates into Ichamati & Churni via Chuadanga",
    "districtsBn": "কুষ্টিয়া, চুয়াডাঙ্গা",
    "districtsEn": "Kushtia, Chuadanga",
    "tributariesBn": "চুয়াডাঙ্গা শহর মাথাভাঙ্গা নদীর তীরে অবস্থিত",
    "tributariesEn": "Chuadanga town lies on the Mathabhanga River",
    "summaryBn": "কুষ্টিয়া ও চুয়াডাঙ্গা জেলার প্রধান সীমান্তবর্তী নদী, যা নবগঙ্গা ও কুমার নদীর উৎস।",
    "summaryEn": "Transboundary distributary of the Padma flowing through Kushtia and Chuadanga.",
    "examPointsBn": [
      "চুয়াডাঙ্গা জেলা শহর মাথাভাঙ্গা নদীর তীরে অবস্থিত",
      "বাংলাদেশ-ভারত অভিন্ন (৫৪টি) নদীর একটি"
    ],
    "examPointsEn": [
      "Chuadanga town sits on the Mathabhanga River",
      "One of the 54 Bangladesh–India transboundary rivers"
    ],
    "coords": [
      [
        88.72,
        24.08
      ],
      [
        88.78,
        23.82
      ],
      [
        88.85,
        23.64
      ],
      [
        88.72,
        23.48
      ]
    ]
  },
  {
    "id": "kumar",
    "nameBn": "কুমার নদ",
    "nameEn": "Kumar River",
    "lengthKm": "১৩০ কি.মি.",
    "lengthKmEn": "130 km",
    "originBn": "চুয়াডাঙ্গা-কুষ্টিয়ায় মাথাভাঙ্গা থেকে উৎপন্ন",
    "originEn": "Offtake from Mathabhanga in Chuadanga–Kushtia",
    "mouthBn": "ফরিদপুর-মাদারীপুরে আড়িয়াল খাঁ নদীতে",
    "mouthEn": "Joins Arial Khan River in Faridpur–Madaripur",
    "districtsBn": "কুষ্টিয়া, ঝিনাইদহ, মাগুরা, ফরিদপুর, মাদারীপুর",
    "districtsEn": "Kushtia, Jhenaidah, Magura, Faridpur, Madaripur",
    "tributariesBn": "ফরিদপুর শহর কুমার নদের তীরে অবস্থিত",
    "tributariesEn": "Faridpur town sits on the Kumar River",
    "summaryBn": "ফরিদপুর ও মাগুরা অঞ্চলের ঐতিহাসিক নদ। পল্লীকবি জসীমউদ্দীনের জন্মভূমি তাম্বুলখানা কুমার নদের তীরে।",
    "summaryEn": "Historic river of Faridpur; flows near Poet Jasimuddin’s ancestral village.",
    "examPointsBn": [
      "ফরিদপুর জেলা শহর কুমার নদের তীরে অবস্থিত",
      "পল্লীকবি জসীমউদ্দীনের স্মৃতিবিজড়িত নদ"
    ],
    "examPointsEn": [
      "Faridpur town lies on the Kumar River",
      "Associated with Pastoral Poet Jasimuddin"
    ],
    "coords": [
      [
        89.15,
        23.68
      ],
      [
        89.45,
        23.64
      ],
      [
        89.84,
        23.6
      ],
      [
        90.15,
        23.22
      ]
    ]
  }
];

export const BD_LANDMARKS = [
  {
    "id": "du",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.3928,
      23.734
    ],
    "established": "1921",
    "nameBn": "ঢাকা বিশ্ববিদ্যালয় (DU)",
    "nameEn": "University of Dhaka (DU)",
    "summaryBn": "১৯২১ সালের ১ জুলাই প্রতিষ্ঠিত বাংলাদেশের প্রাচীনতম ও প্রধান পাবলিক বিশ্ববিদ্যালয় (“প্রাচ্যের অক্সফোর্ড”)।",
    "summaryEn": "Founded on 1 July 1921; oldest public university in Bangladesh (\"Oxford of the East\").",
    "examPointsBn": [
      "প্রতিষ্ঠা: ১ জুলাই ১৯২১ (নাথান কমিশনের সুপারিশে)",
      "প্রথম উপাচার্য: স্যার পি জে হার্টগ; প্রথম ছাত্রী: লীলা নাগ",
      "ভাষা আন্দোলন ও মুক্তিযুদ্ধের সূতিকাগার"
    ],
    "examPointsEn": [
      "Founded 1 July 1921 on Nathan Commission recommendations",
      "First VC: Sir P.J. Hartog; first female student: Leela Nag",
      "Epicenter of 1952 Language Movement & 1971 Liberation War"
    ]
  },
  {
    "id": "buet",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.391,
      23.7266
    ],
    "established": "1962",
    "nameBn": "বুয়েট (BUET)",
    "nameEn": "BUET",
    "summaryBn": "বাংলাদেশ প্রকৌশল বিশ্ববিদ্যালয়—১৮৭৬ সালে ঢাকা সার্ভে স্কুল হিসেবে শুরু হয়ে ১৯৬২ সালে বিশ্ববিদ্যালয়ে উন্নীত হয়।",
    "summaryEn": "Bangladesh University of Engineering & Technology; originated in 1876 and became a university in 1962.",
    "examPointsBn": [
      "১৮৭৬ সালে ঢাকা সার্ভে স্কুল → ১৯৪৭ সালে আহসানউল্লাহ ইঞ্জিনিয়ারিং কলেজ → ১৯৬২ সালে বুয়েট",
      "বাংলাদেশের শীর্ষ প্রকৌশল ও স্থাপত্য বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "1876 Dhaka Survey School → 1947 Ahsanullah Engineering College → 1962 BUET",
      "Premier engineering & architecture university of Bangladesh"
    ]
  },
  {
    "id": "ru",
    "category": "university",
    "districtKey": "rajshahi",
    "coords": [
      88.6368,
      24.37
    ],
    "established": "1953",
    "nameBn": "রাজশাহী বিশ্ববিদ্যালয় (RU)",
    "nameEn": "University of Rajshahi (RU)",
    "summaryBn": "১৯৫৩ সালের ৬ জুলাই প্রতিষ্ঠিত বাংলাদেশের ২য় বৃহত্তম পাবলিক বিশ্ববিদ্যালয় (মতিহারের সবুজ চত্বর)।",
    "summaryEn": "Founded on 6 July 1953; second oldest public university in Bangladesh.",
    "examPointsBn": [
      "প্রতিষ্ঠা: ৬ জুলাই ১৯৫৩; প্রথম উপাচার্য: ড. ইতরাত হোসেন জুবেরী",
      "শহীদ ড. শামসুজ্জোহা (১৯৬৯) বাংলাদেশের প্রথম শহীদ বুদ্ধিজীবী"
    ],
    "examPointsEn": [
      "Founded 6 July 1953; first VC: Dr. Itrat Hossain Zuberi",
      "Dr. Shamsuzzoha (1969) was the first martyred intellectual"
    ]
  },
  {
    "id": "cu",
    "category": "university",
    "districtKey": "chattogram",
    "coords": [
      91.7877,
      22.4716
    ],
    "established": "1966",
    "nameBn": "চট্টগ্রাম বিশ্ববিদ্যালয় (CU)",
    "nameEn": "University of Chittagong (CU)",
    "summaryBn": "১৯৬৬ সালের ১৮ নভেম্বর প্রতিষ্ঠিত পাহাড়ঘেরা বাংলাদেশের আয়তনে বৃহত্তম বিশ্ববিদ্যালয় ক্যাম্পাস ও শাটল ট্রেনের জন্য বিখ্যাত।",
    "summaryEn": "Founded 18 Nov 1966; largest university campus by area in Bangladesh, famous for its shuttle train.",
    "examPointsBn": [
      "প্রতিষ্ঠা: ১৮ নভেম্বর ১৯৬৬; আয়তনে দেশের বৃহত্তম ক্যাম্পাস (২,১০০+ একর)",
      "বিশ্বের একমাত্র নিজস্ব শাটল ট্রেন সুবিধাসম্পন্ন বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Founded 18 Nov 1966; largest campus by area (2,100+ acres)",
      "Only university with its own dedicated commuter shuttle train"
    ]
  },
  {
    "id": "bau",
    "category": "university",
    "districtKey": "mymensingh",
    "coords": [
      90.4267,
      24.7245
    ],
    "established": "1961",
    "nameBn": "বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU)",
    "nameEn": "Bangladesh Agricultural University (BAU)",
    "summaryBn": "১৯৬১ সালের ১৮ আগস্ট পুরাতন ব্রহ্মপুত্র নদের তীরে প্রতিষ্ঠিত দক্ষিণ এশিয়ার অন্যতম বৃহৎ কৃষি বিশ্ববিদ্যালয়।",
    "summaryEn": "Founded 18 Aug 1961 on the Old Brahmaputra; premier agricultural university of South Asia.",
    "examPointsBn": [
      "প্রতিষ্ঠা: ১৮ আগস্ট ১৯৬১, ময়মনসিংহ",
      "বাংলাদেশের কৃষি ও মৎস্য গবেষণার শীর্ষ প্রতিষ্ঠান"
    ],
    "examPointsEn": [
      "Founded 18 Aug 1961 in Mymensingh",
      "Leading agricultural, veterinary & fisheries university"
    ]
  },
  {
    "id": "ctg-port",
    "category": "seaport",
    "districtKey": "chattogram",
    "coords": [
      91.8035,
      22.31
    ],
    "established": "1887",
    "nameBn": "চট্টগ্রাম সমুদ্রবন্দর",
    "nameEn": "Port of Chattogram",
    "summaryBn": "কর্ণফুলী নদীর তীরে অবস্থিত বাংলাদেশের বৃহত্তম ও প্রধান সমুদ্রবন্দর—দেশের ৯০%+ সমুদ্রবাণিজ্য এখান দিয়ে সম্পন্ন হয়।",
    "summaryEn": "Bangladesh’s largest and busiest seaport on the Karnaphuli River, handling over 90% of maritime trade.",
    "examPointsBn": [
      "বাংলাদেশের প্রধান ও বৃহত্তম সমুদ্রবন্দর (কর্ণফুলী নদীর তীরে)",
      "বৈদেশিক বাণিজ্যের প্রায় ৯২% এই বন্দরের মাধ্যমে সম্পন্ন হয়"
    ],
    "examPointsEn": [
      "Primary & busiest seaport of Bangladesh on the Karnaphuli",
      "Handles ~92% of Bangladesh’s maritime import/export trade"
    ]
  },
  {
    "id": "mongla-port",
    "category": "seaport",
    "districtKey": "bagerhat",
    "coords": [
      89.5915,
      22.473
    ],
    "established": "1950",
    "nameBn": "মোংলা সমুদ্রবন্দর",
    "nameEn": "Mongla Seaport",
    "summaryBn": "পশুর নদীর তীরে অবস্থিত বাংলাদেশের দ্বিতীয় বৃহত্তম সমুদ্রবন্দর (পূর্বে চালনা বন্দর নামে পরিচিত ছিল)।",
    "summaryEn": "Second largest seaport of Bangladesh on the Pasur River (formerly Chalna Port).",
    "examPointsBn": [
      "প্রতিষ্ঠা: ১ ডিসেম্বর ১৯৫০ (পশুর নদীর তীরে, বাগেরহাট)",
      "বাংলাদেশের ২য় বৃহত্তম সমুদ্রবন্দর"
    ],
    "examPointsEn": [
      "Established 1 Dec 1950 on the Pasur River in Bagerhat",
      "Second largest seaport of Bangladesh"
    ]
  },
  {
    "id": "payra-port",
    "category": "seaport",
    "districtKey": "patuakhali",
    "coords": [
      90.289,
      21.946
    ],
    "established": "2013",
    "nameBn": "পায়রা সমুদ্রবন্দর",
    "nameEn": "Payra Seaport",
    "summaryBn": "পটুয়াখালীর কলাপাড়ায় রাবনাবাদ চ্যানেলে অবস্থিত বাংলাদেশের তৃতীয় সমুদ্রবন্দর।",
    "summaryEn": "Third seaport of Bangladesh located along the Rabnabad Channel in Kalapara, Patuakhali.",
    "examPointsBn": [
      "বাংলাদেশের ৩য় সমুদ্রবন্দর (২০১৩ সালে আইন পাস, ২০১৬ সালে অপারেশন শুরু)",
      "পটুয়াখালীর কলাপাড়ায় রাবনাবাদ চ্যানেলে অবস্থিত"
    ],
    "examPointsEn": [
      "Bangladesh’s 3rd seaport (Act passed 2013, operations began 2016)",
      "Located on the Rabnabad Channel in Kalapara, Patuakhali"
    ]
  },
  {
    "id": "matarbari-port",
    "category": "seaport",
    "districtKey": "coxs-bazar",
    "coords": [
      91.871,
      21.703
    ],
    "established": "2020",
    "nameBn": "মাতারবাড়ী গভীর সমুদ্রবন্দর",
    "nameEn": "Matarbari Deep Sea Port",
    "summaryBn": "কক্সবাজারের মহেশখালীতে জাপানের (JICA) সহায়তায় নির্মিতব্য বাংলাদেশের প্রথম গভীর সমুদ্রবন্দর ও ১২০০ মেগাওয়াট বিদ্যুৎকেন্দ্র।",
    "summaryEn": "Bangladesh’s first deep-sea port & 1,200 MW power hub under development in Moheshkhali with JICA support.",
    "examPointsBn": [
      "বাংলাদেশের প্রথম গভীর সমুদ্রবন্দর (মহেশখালী, কক্সবাজার)",
      "জাপানের (JICA) কারিগরি ও আর্থিক সহায়তায় নির্মিত"
    ],
    "examPointsEn": [
      "Bangladesh’s first deep-sea port (Moheshkhali, Cox’s Bazar)",
      "Constructed with Japan (JICA) technical & financial cooperation"
    ]
  },
  {
    "id": "hsia",
    "category": "airport",
    "districtKey": "dhaka",
    "coords": [
      90.4005,
      23.8433
    ],
    "established": "1980",
    "nameBn": "হযরত শাহজালাল আন্তর্জাতিক বিমানবন্দর",
    "nameEn": "Hazrat Shahjalal Intl. Airport (DAC)",
    "summaryBn": "ঢাকার কুর্মিটোলায় অবস্থিত বাংলাদেশের বৃহত্তম ও প্রধান আন্তর্জাতিক বিমানবন্দর (থার্ড টার্মিনাল যুক্ত)।",
    "summaryEn": "Largest international airport in Bangladesh (IATA: DAC), located in Kurmitola, Dhaka.",
    "examPointsBn": [
      "IATA কোড: DAC; বাংলাদেশের প্রধান আন্তর্জাতিক বিমানবন্দর (১৯৮০ সালে চালু)",
      "নতুন থার্ড টার্মিনালের স্থপতি: রোহানি বাহারিন"
    ],
    "examPointsEn": [
      "IATA code: DAC; opened in 1980 as Bangladesh’s main gateway",
      "Third Terminal architect: Rohani Baharin"
    ]
  },
  {
    "id": "padma-bridge",
    "category": "mega_project",
    "districtKey": "munshiganj",
    "coords": [
      90.2625,
      23.446
    ],
    "established": "2022",
    "nameBn": "পদ্মা বহুমুখী সেতু",
    "nameEn": "Padma Multipurpose Bridge",
    "summaryBn": "নিজস্ব অর্থায়নে নির্মিত ৬.১৫ কি.মি. দীর্ঘ দোতলা (সড়ক ও রেল) সেতু—২৫ জুন ২০২২ উদ্বোধন।",
    "summaryEn": "Self-financed 6.15 km two-level road-rail truss bridge connecting Mawa & Jajira; inaugurated 25 June 2022.",
    "examPointsBn": [
      "দৈর্ঘ্য: ৬.১৫ কি.মি. (৪১টি স্প্যান, ৪২টি পিলার), উদ্বোধন: ২৫ জুন ২০২২",
      "মাওয়া (মুন্সীগঞ্জ) ও জাজিরা (শরীয়তপুর) যুক্ত করেছে",
      "বাংলাদেশের নিজস্ব অর্থায়নে নির্মিত বৃহত্তম মেগা প্রকল্প"
    ],
    "examPointsEn": [
      "Length: 6.15 km (41 spans, 42 piers); opened 25 June 2022",
      "Connects Mawa (Munshiganj) and Jajira (Shariatpur)",
      "Largest self-funded infrastructure project in Bangladesh"
    ]
  },
  {
    "id": "jamuna-bridge",
    "category": "mega_project",
    "districtKey": "sirajganj",
    "coords": [
      89.785,
      24.3995
    ],
    "established": "1998",
    "nameBn": "যমুনা বহুমুখী সেতু",
    "nameEn": "Jamuna Multipurpose Bridge",
    "summaryBn": "১৯৯৮ সালের ২৩ জুন উদ্বোধনকৃত ৪.৮ কি.মি. দীর্ঘ সেতু যা ঢাকা ও উত্তরবঙ্গকে সরাসরি যুক্ত করেছে।",
    "summaryEn": "4.8 km bridge opened on 23 June 1998 linking Tangail and Sirajganj.",
    "examPointsBn": [
      "দৈর্ঘ্য: ৪.৮ কি.মি. (৪৯টি স্প্যান, ৫০টি পিলার), উদ্বোধন: ২৩ জুন ১৯৯৮",
      "টাঙ্গাইল ও সিরাজগঞ্জ জেলাকে সংযুক্ত করেছে"
    ],
    "examPointsEn": [
      "Length: 4.8 km (49 spans, 50 piers); opened 23 June 1998",
      "Links Tangail and Sirajganj across the Jamuna River"
    ]
  },
  {
    "id": "karnaphuli-tunnel",
    "category": "mega_project",
    "districtKey": "chattogram",
    "coords": [
      91.815,
      22.238
    ],
    "established": "2023",
    "nameBn": "কর্ণফুলী টানেল",
    "nameEn": "Karnaphuli Underwater Tunnel",
    "summaryBn": "চট্টগ্রামের পতেঙ্গা ও আনোয়ারাকে যুক্তকারী দক্ষিণ এশিয়ার প্রথম নদীর তলদেশীয় সড়ক টানেল (২৮ অক্টোবর ২০২৩ উদ্বোধন)।",
    "summaryEn": "South Asia’s first underwater river road tunnel connecting Patenga & Anwara; opened 28 Oct 2023.",
    "examPointsBn": [
      "মূল টানেলের দৈর্ঘ্য ৩.৩২ কি.মি. (মোট প্রকল্প ৯.৩৯ কি.মি.)",
      "উদ্বোধন: ২৮ অক্টোবর ২০২৩; দক্ষিণ এশিয়ার প্রথম আন্ডারওয়াটার রোড টানেল"
    ],
    "examPointsEn": [
      "Main tunnel length: 3.32 km (total 9.39 km with approaches)",
      "Opened 28 Oct 2023; South Asia’s first underwater road tunnel"
    ]
  },
  {
    "id": "rooppur-npp",
    "category": "power_plant",
    "districtKey": "pabna",
    "coords": [
      89.047,
      24.0665
    ],
    "established": "2017",
    "nameBn": "রূপপুর পারমাণবিক বিদ্যুৎকেন্দ্র",
    "nameEn": "Rooppur Nuclear Power Plant",
    "summaryBn": "পাবনার ঈশ্বরদীতে রাশিয়ার রোসাটমের সহায়তায় নির্মিতব্য ২,৪০০ মেগাওয়াট (২×১২০০) ক্ষমতার বাংলাদেশের প্রথম পারমাণবিক বিদ্যুৎকেন্দ্র।",
    "summaryEn": "Bangladesh’s first nuclear power plant (2,400 MW VVER-1200) in Ishwardi, Pabna with Rosatom.",
    "examPointsBn": [
      "অবস্থান: ঈশ্বরদী, পাবনা (পদ্মা নদীর তীরে); উৎপাদন ক্ষমতা: ২,৪০০ মেগাওয়াট",
      "বিশ্বের ৩৩তম পারমাণবিক বিদ্যুৎ উৎপাদনকারী দেশ হিসেবে বাংলাদেশ"
    ],
    "examPointsEn": [
      "Location: Ishwardi, Pabna (on Padma River); Capacity: 2,400 MW",
      "Makes Bangladesh the 33rd nuclear power nation globally"
    ]
  },
  {
    "id": "sundarbans",
    "category": "heritage",
    "districtKey": "khulna",
    "coords": [
      89.45,
      22.05
    ],
    "established": "1997 (UNESCO)",
    "nameBn": "সুন্দরবন (UNESCO বিশ্ব ঐতিহ্য)",
    "nameEn": "The Sundarbans (UNESCO)",
    "summaryBn": "পৃথিবীর বৃহত্তম ম্যানগ্রোভ বন (মোট ১০,০০০ বর্গ কি.মি., বাংলাদেশে ৬,০১৭ বর্গ কি.মি.) এবং রয়েল বেঙ্গল টাইগারের আবাসস্থল।",
    "summaryEn": "World’s largest mangrove forest (6,017 km² in Bangladesh) and home of the Royal Bengal Tiger.",
    "examPointsBn": [
      "১৯৯৭ সালের ৬ ডিসেম্বর UNESCO বিশ্ব ঐতিহ্য (৭৯৮তম) ও ১৯৯২ সালে ১ম রামসার সাইট",
      "মোট আয়তনের প্রায় ৬০.১৭% বাংলাদেশে (খুলনা, সাতক্ষীরা, বাগেরহাট, বরগুনা, পটুয়াখালী)",
      "১৪ ফেব্রুয়ারি সুন্দরবন দিবস"
    ],
    "examPointsEn": [
      "UNESCO World Heritage (6 Dec 1997) & 1st Ramsar Site (1992)",
      "6,017 km² lies in Bangladesh (~60% of total forest)",
      "Sundarbans Day is observed on 14 February"
    ]
  },
  {
    "id": "sixty-dome",
    "category": "heritage",
    "districtKey": "bagerhat",
    "coords": [
      89.7418,
      22.6745
    ],
    "established": "1985 (UNESCO)",
    "nameBn": "ষাট গম্বুজ মসজিদ (UNESCO)",
    "nameEn": "Sixty Dome Mosque (UNESCO)",
    "summaryBn": "পঞ্চদশ শতকে হযরত খানজাহান আলী (র.) নির্মিত বাংলাদেশের বৃহত্তম ঐতিহাসিক মসজিদ (১৯৮৫ সালে UNESCO স্বীকৃত)।",
    "summaryEn": "15th-century mosque built by Khan Jahan Ali in Bagerhat; inscribed as UNESCO World Heritage in 1985.",
    "examPointsBn": [
      "প্রকৃত গম্বুজ ৮১টি (৭৭টি বর্গাকার + ৪টি কর্নার), স্তম্ভ ৬০টি",
      "১৯৮৫ সালে UNESCO বিশ্ব ঐতিহ্য হিসেবে স্বীকৃত (বাগেরহাট)"
    ],
    "examPointsEn": [
      "Has 81 domes in total and 60 stone pillars",
      "Inscribed as UNESCO World Heritage Site in 1985"
    ]
  },
  {
    "id": "paharpur",
    "category": "heritage",
    "districtKey": "naogaon",
    "coords": [
      88.9772,
      25.031
    ],
    "established": "1985 (UNESCO)",
    "nameBn": "পাহাড়পুর বৌদ্ধ বিহার (UNESCO)",
    "nameEn": "Paharpur Buddhist Vihara (UNESCO)",
    "summaryBn": "পাল সম্রাট ধর্মপাল নির্মিত (৮ম শতক) ভারতীয় উপমহাদেশের অন্যতম বৃহৎ বৌদ্ধ বিহার—সোমপুর মহাবিহার।",
    "summaryEn": "8th-century Somapura Mahavihara built by Pala King Dharmapala in Naogaon; UNESCO Heritage since 1985.",
    "examPointsBn": [
      "আসল নাম: সোমপুর মহাবিহার; নির্মাতা: পাল রাজা ধর্মপাল (নওগাঁ)",
      "১৯৮৫ সালে UNESCO বিশ্ব ঐতিহ্যের তালিকাভুক্ত"
    ],
    "examPointsEn": [
      "Original name: Somapura Mahavihara; built by Pala King Dharmapala",
      "UNESCO World Heritage Site since 1985"
    ]
  },
  {
    "id": "national-memorial",
    "category": "historic",
    "districtKey": "dhaka",
    "coords": [
      90.2548,
      23.9112
    ],
    "established": "1982",
    "nameBn": "জাতীয় স্মৃতিসৌধ (সাভার)",
    "nameEn": "National Martyrs’ Memorial (Savar)",
    "summaryBn": "মুক্তিযুদ্ধের বীর শহীদদের স্মরণে সাভারে নির্মিত ৭টি স্তম্ভবিশিষ্ট জাতীয় স্মৃতিসৌধ।",
    "summaryEn": "Iconic 7-spire memorial at Savar honoring the martyrs of the 1971 Liberation War.",
    "examPointsBn": [
      "স্থপতি: সৈয়দ মাইনুল হোসেন; উচ্চতা: ১৫০ ফুট (৪৫.৭২ মিটার)",
      "৭টি ফলক ১৯৫২, ১৯৫৪, ১৯৫৬, ১৯৬২, ১৯৬৬, ১৯৬৯ ও ১৯৭১-এর প্রতীক"
    ],
    "examPointsEn": [
      "Architect: Syed Mainul Hossain; height: 150 ft (45.72 m)",
      "7 spires symbolize 1952, 1954, 1956, 1962, 1966, 1969 & 1971"
    ]
  },
  {
    "id": "mujibnagar",
    "category": "historic",
    "districtKey": "meherpur",
    "coords": [
      88.6015,
      23.6352
    ],
    "established": "1971",
    "nameBn": "মুজিবনগর স্মৃতিসৌধ",
    "nameEn": "Mujibnagar Memorial",
    "summaryBn": "মেহেরপুরের বৈদ্যনাথতলায় ১৯৭১ সালের ১৭ এপ্রিল বাংলাদেশের প্রথম অস্থায়ী সরকারের শপথস্থান।",
    "summaryEn": "Site in Meherpur where Bangladesh’s first provisional government took oath on 17 April 1971.",
    "examPointsBn": [
      "১৭ এপ্রিল ১৯৭১—মুজিবনগর দিবস (প্রথম সরকারের শপথ)",
      "স্থপতি: তানভীর করিম; ২৩টি স্তম্ভ ১৯৪৮–১৯৭১ এর প্রতীক"
    ],
    "examPointsEn": [
      "17 April 1971 — Mujibnagar Day (oath of 1st Government)",
      "Architect: Tanvir Karim; 23 pillars symbolize 1948–1971"
    ]
  },
  {
    "id": "cepz",
    "category": "epz",
    "districtKey": "chattogram",
    "coords": [
      91.778,
      22.297
    ],
    "established": "1983",
    "nameBn": "চট্টগ্রাম ইপিজেড (CEPZ)",
    "nameEn": "Chattogram EPZ (CEPZ)",
    "summaryBn": "১৯৮৩ সালে প্রতিষ্ঠিত বাংলাদেশের প্রথম ও বৃহত্তম রপ্তানি প্রক্রিয়াকরণ অঞ্চল (BEPZA পরিচালিত)।",
    "summaryEn": "Bangladesh’s first and largest Export Processing Zone, established in 1983 under BEPZA.",
    "examPointsBn": [
      "বাংলাদেশের প্রথম ইপিজেড—চট্টগ্রাম ইপিজেড (১৯৮৩)",
      "বাংলাদেশে সরকারি মোট ৮টি EPZ রয়েছে (BEPZA-র অধীনে)"
    ],
    "examPointsEn": [
      "First EPZ of Bangladesh (established 1983)",
      "Bangladesh operates 8 public EPZs under BEPZA"
    ]
  },
  {
    "id": "depz",
    "category": "epz",
    "districtKey": "dhaka",
    "coords": [
      90.274,
      23.949
    ],
    "established": "1993",
    "nameBn": "ঢাকা ইপিজেড (DEPZ, সাভার)",
    "nameEn": "Dhaka EPZ (DEPZ, Savar)",
    "summaryBn": "১৯৯৩ সালে সাভারে প্রতিষ্ঠিত দেশের ২য় রপ্তানি প্রক্রিয়াকরণ অঞ্চল।",
    "summaryEn": "Second EPZ of Bangladesh established in 1993 at Savar, Dhaka.",
    "examPointsBn": [
      "প্রতিষ্ঠা: ১৯৯৩ (সাভার, ঢাকা)",
      "তৈরি পোশাক ও রপ্তানিমুখী শিল্পের অন্যতম প্রধান কেন্দ্র"
    ],
    "examPointsEn": [
      "Established in 1993 at Savar, Dhaka",
      "Major export-oriented garment & industrial manufacturing hub"
    ]
  },
  {
    "id": "mahasthangarh",
    "category": "historic",
    "districtKey": "bogura",
    "coords": [
      89.3428,
      24.9612
    ],
    "established": "3rd c. BCE",
    "nameBn": "মহাস্থানগড় (পুণ্ড্রনগর)",
    "nameEn": "Mahasthangarh (Pundranagara)",
    "summaryBn": "করতোয়া নদীর তীরে বগুড়ায় অবস্থিত বাংলাদেশের প্রাচীনতম প্রত্নতাত্ত্বিক দুর্গনগরী।",
    "summaryEn": "Oldest known urban archaeological site in Bangladesh (~3rd century BCE) on the Karatoya River.",
    "examPointsBn": [
      "প্রাচীন পুণ্ড্রবর্ধন জনপদের রাজধানী পুণ্ড্রনগর",
      "ব্রাহ্মী লিপিতে খোদাইকৃত প্রাচীনতম শিলালিপি এখানেই পাওয়া গেছে"
    ],
    "examPointsEn": [
      "Capital Pundranagara of ancient Pundravardhana",
      "Site of Bangladesh’s oldest Brahmi inscription"
    ]
  },
  {
    "id": "uni-iu",
    "category": "university",
    "districtKey": "kushtia",
    "coords": [
      89.0281,
      23.943
    ],
    "established": "1979",
    "nameBn": "ইসলামী বিশ্ববিদ্যালয়, বাংলাদেশ (IU)",
    "nameEn": "Islamic University, Bangladesh (IU)",
    "summaryBn": "1979 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: কুষ্টিয়া (Kushtia Sadar, Kushtia)।",
    "summaryEn": "Public General Public University of Bangladesh established in 1979, located in Kushtia Sadar, Kushtia (Kushtia).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1979 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Kushtia Sadar, Kushtia (কুষ্টিয়া জেলা); সংক্ষিপ্ত নাম: IU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1979; Type: Public General Public University",
      "Location: Kushtia Sadar, Kushtia (Kushtia District); Acronym: IU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-ku",
    "category": "university",
    "districtKey": "khulna",
    "coords": [
      89.5075,
      22.3852
    ],
    "established": "1991",
    "nameBn": "খুলনা বিশ্ববিদ্যালয় (KU)",
    "nameEn": "Khulna University (KU)",
    "summaryBn": "1991 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: খুলনা (Gallamari, Khulna)।",
    "summaryEn": "Public General Public University of Bangladesh established in 1991, located in Gallamari, Khulna (Khulna).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1991 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Gallamari, Khulna (খুলনা জেলা); সংক্ষিপ্ত নাম: KU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1991; Type: Public General Public University",
      "Location: Gallamari, Khulna (Khulna District); Acronym: KU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-jnu",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.3262,
      23.7434
    ],
    "established": "2005",
    "nameBn": "জগন্নাথ বিশ্ববিদ্যালয় (JnU)",
    "nameEn": "Jagannath University (JnU)",
    "summaryBn": "2005 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Sadar Ghat, Dhaka)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2005, located in Sadar Ghat, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2005 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Sadar Ghat, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: JnU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2005; Type: Public General Public University",
      "Location: Sadar Ghat, Dhaka (Dhaka District); Acronym: JnU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-cou",
    "category": "university",
    "districtKey": "cumilla",
    "coords": [
      90.9993,
      23.4218
    ],
    "established": "2006",
    "nameBn": "কুমিল্লা বিশ্ববিদ্যালয় (CoU)",
    "nameEn": "Comilla University (CoU)",
    "summaryBn": "2006 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: কুমিল্লা (Sadar Dakkhin, Comilla)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2006, located in Sadar Dakkhin, Comilla (Cumilla).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2006 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Sadar Dakkhin, Comilla (কুমিল্লা জেলা); সংক্ষিপ্ত নাম: CoU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2006; Type: Public General Public University",
      "Location: Sadar Dakkhin, Comilla (Cumilla District); Acronym: CoU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-jkkniu",
    "category": "university",
    "districtKey": "mymensingh",
    "coords": [
      90.4632,
      24.7544
    ],
    "established": "2006",
    "nameBn": "জাতীয় কবি কাজী নজরুল ইসলাম বিশ্ববিদ্যালয় (JKKNIU)",
    "nameEn": "Jatiya Kabi Kazi Nazrul Islam University (JKKNIU)",
    "summaryBn": "2006 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ময়মনসিংহ (Trishal, Mymensingh)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2006, located in Trishal, Mymensingh (Mymensingh).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2006 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Trishal, Mymensingh (ময়মনসিংহ জেলা); সংক্ষিপ্ত নাম: JKKNIU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2006; Type: Public General Public University",
      "Location: Trishal, Mymensingh (Mymensingh District); Acronym: JKKNIU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-bup",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.3288,
      23.8328
    ],
    "established": "2008",
    "nameBn": "বাংলাদেশ ইউনিভার্সিটি অব প্রফেশনালস (BUP)",
    "nameEn": "Bangladesh University of Professionals (BUP)",
    "summaryBn": "2008 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Mirpur, Dhaka)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2008, located in Mirpur, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2008 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Mirpur, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: BUP",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2008; Type: Public General Public University",
      "Location: Mirpur, Dhaka (Dhaka District); Acronym: BUP",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-brur",
    "category": "university",
    "districtKey": "rangpur",
    "coords": [
      89.2338,
      25.6328
    ],
    "established": "2008",
    "nameBn": "বেগম রোকেয়া বিশ্ববিদ্যালয়, রংপুর (BRUR)",
    "nameEn": "Begum Rokeya University (BRUR)",
    "summaryBn": "2008 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: রংপুর (Kotwali)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2008, located in Kotwali (Rangpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2008 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Kotwali (রংপুর জেলা); সংক্ষিপ্ত নাম: BRUR",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2008; Type: Public General Public University",
      "Location: Kotwali (Rangpur District); Acronym: BRUR",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-bu",
    "category": "university",
    "districtKey": "barishal",
    "coords": [
      90.3535,
      22.7627
    ],
    "established": "2011",
    "nameBn": "বরিশাল বিশ্ববিদ্যালয় (BU)",
    "nameEn": "University of Barisal (BU)",
    "summaryBn": "2011 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: বরিশাল (Bandar, Barisal)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2011, located in Bandar, Barisal (Barishal).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2011 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Bandar, Barisal (বরিশাল জেলা); সংক্ষিপ্ত নাম: BU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2011; Type: Public General Public University",
      "Location: Bandar, Barisal (Barishal District); Acronym: BU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-rub",
    "category": "university",
    "districtKey": "sirajganj",
    "coords": [
      89.5503,
      24.4306
    ],
    "established": "2017",
    "nameBn": "রবীন্দ্র বিশ্ববিদ্যালয়, বাংলাদেশ (RUB)",
    "nameEn": "Rabindra University, Bangladesh (RUB)",
    "summaryBn": "2017 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: সিরাজগঞ্জ (Shahjadpur, Sirajganj)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2017, located in Shahjadpur, Sirajganj (Sirajganj).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2017 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Shahjadpur, Sirajganj (সিরাজগঞ্জ জেলা); সংক্ষিপ্ত নাম: RUB",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2017; Type: Public General Public University",
      "Location: Shahjadpur, Sirajganj (Sirajganj District); Acronym: RUB",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-nku",
    "category": "university",
    "districtKey": "netrokona",
    "coords": [
      90.8674,
      24.886
    ],
    "established": "2018",
    "nameBn": "নেত্রকোনা বিশ্ববিদ্যালয় (NkU)",
    "nameEn": "Netrokona University (NkU)",
    "summaryBn": "2018 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: নেত্রকোণা (Netrokona Sadar, Netrokona)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2018, located in Netrokona Sadar, Netrokona (Netrokona).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2018 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Netrokona Sadar, Netrokona (নেত্রকোণা জেলা); সংক্ষিপ্ত নাম: NkU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2018; Type: Public General Public University",
      "Location: Netrokona Sadar, Netrokona (Netrokona District); Acronym: NkU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-kgu",
    "category": "university",
    "districtKey": "kishoreganj",
    "coords": [
      90.9148,
      24.3368
    ],
    "established": "2020",
    "nameBn": "কিশোরগঞ্জ বিশ্ববিদ্যালয় (KgU)",
    "nameEn": "Kishoreganj University (KgU)",
    "summaryBn": "2020 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: কিশোরগঞ্জ (Kishoreganj Sadar, Kishoreganj)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2020, located in Kishoreganj Sadar, Kishoreganj (Kishoreganj).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2020 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Kishoreganj Sadar, Kishoreganj (কিশোরগঞ্জ জেলা); সংক্ষিপ্ত নাম: KgU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2020; Type: Public General Public University",
      "Location: Kishoreganj Sadar, Kishoreganj (Kishoreganj District); Acronym: KgU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-mu",
    "category": "university",
    "districtKey": "meherpur",
    "coords": [
      88.7344,
      23.8202
    ],
    "established": "2022",
    "nameBn": "মেহেরপুর বিশ্ববিদ্যালয় (MU)",
    "nameEn": "Meherpur University (MU)",
    "summaryBn": "2022 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: মেহেরপুর (Mujibnagar, Meherpur)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2022, located in Mujibnagar, Meherpur (Meherpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2022 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Mujibnagar, Meherpur (মেহেরপুর জেলা); সংক্ষিপ্ত নাম: MU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2022; Type: Public General Public University",
      "Location: Mujibnagar, Meherpur (Meherpur District); Acronym: MU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-ub",
    "category": "university",
    "districtKey": "bogura",
    "coords": [
      89.3606,
      24.8624
    ],
    "established": "2001",
    "nameBn": "বগুড়া বিশ্ববিদ্যালয় (UB)",
    "nameEn": "University of Bogura (UB)",
    "summaryBn": "2001 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: বগুড়া (Bogura Sadar, Bogura)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2001, located in Bogura Sadar, Bogura (Bogura).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2001 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Bogura Sadar, Bogura (বগুড়া জেলা); সংক্ষিপ্ত নাম: UB",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2001; Type: Public General Public University",
      "Location: Bogura Sadar, Bogura (Bogura District); Acronym: UB",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-tu",
    "category": "university",
    "districtKey": "thakurgaon",
    "coords": [
      88.3662,
      25.9364
    ],
    "established": "2022",
    "nameBn": "ঠাকুরগাঁও বিশ্ববিদ্যালয় (TU)",
    "nameEn": "Thakurgaon University (TU)",
    "summaryBn": "2022 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ঠাকুরগাঁও (Thakurgaon Sadar, Thakurgaon)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2022, located in Thakurgaon Sadar, Thakurgaon (Thakurgaon).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2022 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Thakurgaon Sadar, Thakurgaon (ঠাকুরগাঁও জেলা); সংক্ষিপ্ত নাম: TU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2022; Type: Public General Public University",
      "Location: Thakurgaon Sadar, Thakurgaon (Thakurgaon District); Acronym: TU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-nau",
    "category": "university",
    "districtKey": "naogaon",
    "coords": [
      88.7812,
      24.9055
    ],
    "established": "2023",
    "nameBn": "নওগাঁ বিশ্ববিদ্যালয় (NaU)",
    "nameEn": "Naogaon University (NaU)",
    "summaryBn": "2023 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়, অবস্থান: নওগাঁ (Naogaon Sadar, Naogaon)।",
    "summaryEn": "Public General Public University of Bangladesh established in 2023, located in Naogaon Sadar, Naogaon (Naogaon).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2023 সাল; ধরন: সরকারি সাধারণ পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Naogaon Sadar, Naogaon (নওগাঁ জেলা); সংক্ষিপ্ত নাম: NaU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2023; Type: Public General Public University",
      "Location: Naogaon Sadar, Naogaon (Naogaon District); Acronym: NaU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-sust",
    "category": "university",
    "districtKey": "sylhet",
    "coords": [
      92.0642,
      24.8903
    ],
    "established": "1986",
    "nameBn": "শাহজালাল বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (SUST)",
    "nameEn": "Shahjalal University of Science and Technology (SUST)",
    "summaryBn": "1986 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: সিলেট (Kumargaon, Sylhet)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 1986, located in Kumargaon, Sylhet (Sylhet).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1986 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Kumargaon, Sylhet (সিলেট জেলা); সংক্ষিপ্ত নাম: SUST",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1986; Type: Public Science & Technology University",
      "Location: Kumargaon, Sylhet (Sylhet District); Acronym: SUST",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-hstu",
    "category": "university",
    "districtKey": "dinajpur",
    "coords": [
      88.8436,
      25.6426
    ],
    "established": "1999",
    "nameBn": "হাজী মোহাম্মদ দানেশ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (HSTU)",
    "nameEn": "Hajee Mohammad Danesh Science & Technology University (HSTU)",
    "summaryBn": "1999 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: দিনাজপুর (Kotwali, Dinajpur)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 1999, located in Kotwali, Dinajpur (Dinajpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1999 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Kotwali, Dinajpur (দিনাজপুর জেলা); সংক্ষিপ্ত নাম: HSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1999; Type: Public Science & Technology University",
      "Location: Kotwali, Dinajpur (Dinajpur District); Acronym: HSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-mbstu",
    "category": "university",
    "districtKey": "tangail",
    "coords": [
      90.0174,
      24.3768
    ],
    "established": "1999",
    "nameBn": "মাওলানা ভাসানী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (MBSTU)",
    "nameEn": "Mawlana Bhashani Science and Technology University (MBSTU)",
    "summaryBn": "1999 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: টাঙ্গাইল (Tangail Sadar, Tangail)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 1999, located in Tangail Sadar, Tangail (Tangail).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1999 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Tangail Sadar, Tangail (টাঙ্গাইল জেলা); সংক্ষিপ্ত নাম: MBSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1999; Type: Public Science & Technology University",
      "Location: Tangail Sadar, Tangail (Tangail District); Acronym: MBSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-pstu",
    "category": "university",
    "districtKey": "patuakhali",
    "coords": [
      90.3869,
      22.2128
    ],
    "established": "2000",
    "nameBn": "পটুয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (PSTU)",
    "nameEn": "Patuakhali Science and Technology University (PSTU)",
    "summaryBn": "2000 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: পটুয়াখালী (Dumki, Patuakhali)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2000, located in Dumki, Patuakhali (Patuakhali).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2000 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Dumki, Patuakhali (পটুয়াখালী জেলা); সংক্ষিপ্ত নাম: PSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2000; Type: Public Science & Technology University",
      "Location: Dumki, Patuakhali (Patuakhali District); Acronym: PSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-nstu",
    "category": "university",
    "districtKey": "noakhali",
    "coords": [
      91.1553,
      22.5782
    ],
    "established": "2006",
    "nameBn": "নোয়াখালী বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (NSTU)",
    "nameEn": "Noakhali Science and Technology University (NSTU)",
    "summaryBn": "2006 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: নোয়াখালী (Sudharam, Noakhali)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2006, located in Sudharam, Noakhali (Noakhali).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2006 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Sudharam, Noakhali (নোয়াখালী জেলা); সংক্ষিপ্ত নাম: NSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2006; Type: Public Science & Technology University",
      "Location: Sudharam, Noakhali (Noakhali District); Acronym: NSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-just",
    "category": "university",
    "districtKey": "jashore",
    "coords": [
      89.2049,
      23.0849
    ],
    "established": "2007",
    "nameBn": "যশোর বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (JUST)",
    "nameEn": "Jashore University of Science and Technology (JUST)",
    "summaryBn": "2007 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: যশোর (Kotwali, Jessore)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2007, located in Kotwali, Jessore (Jashore).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2007 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Kotwali, Jessore (যশোর জেলা); সংক্ষিপ্ত নাম: JUST",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2007; Type: Public Science & Technology University",
      "Location: Kotwali, Jessore (Jashore District); Acronym: JUST",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-pust",
    "category": "university",
    "districtKey": "pabna",
    "coords": [
      89.3792,
      24.1129
    ],
    "established": "2008",
    "nameBn": "পাবনা বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (PUST)",
    "nameEn": "Pabna University of Science and Technology (PUST)",
    "summaryBn": "2008 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: পাবনা (Kotwali, Pabna)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2008, located in Kotwali, Pabna (Pabna).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2008 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Kotwali, Pabna (পাবনা জেলা); সংক্ষিপ্ত নাম: PUST",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2008; Type: Public Science & Technology University",
      "Location: Kotwali, Pabna (Pabna District); Acronym: PUST",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-gstu",
    "category": "university",
    "districtKey": "gopalganj",
    "coords": [
      89.9076,
      23.1067
    ],
    "established": "2011",
    "nameBn": "গোপালগঞ্জ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (GSTU)",
    "nameEn": "Gopalganj Science and Technology University (GSTU)",
    "summaryBn": "2011 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: গোপালগঞ্জ (Gopalganj Sadar, Gopalganj)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2011, located in Gopalganj Sadar, Gopalganj (Gopalganj).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2011 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Gopalganj Sadar, Gopalganj (গোপালগঞ্জ জেলা); সংক্ষিপ্ত নাম: GSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2011; Type: Public Science & Technology University",
      "Location: Gopalganj Sadar, Gopalganj (Gopalganj District); Acronym: GSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-rmstu",
    "category": "university",
    "districtKey": "rangamati",
    "coords": [
      92.2809,
      22.7961
    ],
    "established": "2014",
    "nameBn": "রাঙামাটি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (RmSTU)",
    "nameEn": "Rangamati Science and Technology University (RmSTU)",
    "summaryBn": "2014 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: রাঙামাটি (Kotwali, Rangamati)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2014, located in Kotwali, Rangamati (Rangamati).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2014 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Kotwali, Rangamati (রাঙামাটি জেলা); সংক্ষিপ্ত নাম: RmSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2014; Type: Public Science & Technology University",
      "Location: Kotwali, Rangamati (Rangamati District); Acronym: RmSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-jstu",
    "category": "university",
    "districtKey": "jamalpur",
    "coords": [
      89.9202,
      25.0002
    ],
    "established": "2017",
    "nameBn": "জামালপুর বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (JSTU)",
    "nameEn": "Jamalpur Science and Technology University (JSTU)",
    "summaryBn": "2017 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: জামালপুর (Melandaha, Jamalpur)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2017, located in Melandaha, Jamalpur (Jamalpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2017 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Melandaha, Jamalpur (জামালপুর জেলা); সংক্ষিপ্ত নাম: JSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2017; Type: Public Science & Technology University",
      "Location: Melandaha, Jamalpur (Jamalpur District); Acronym: JSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-cstu",
    "category": "university",
    "districtKey": "chandpur",
    "coords": [
      90.7844,
      23.2441
    ],
    "established": "2020",
    "nameBn": "চাঁদপুর বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (CSTU)",
    "nameEn": "Chandpur Science and Technology University (CSTU)",
    "summaryBn": "2020 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: চাঁদপুর (Chandpur Sadar, Chandpur)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2020, located in Chandpur Sadar, Chandpur (Chandpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2020 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Chandpur Sadar, Chandpur (চাঁদপুর জেলা); সংক্ষিপ্ত নাম: CSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2020; Type: Public Science & Technology University",
      "Location: Chandpur Sadar, Chandpur (Chandpur District); Acronym: CSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-sstu",
    "category": "university",
    "districtKey": "sunamganj",
    "coords": [
      91.3307,
      24.8882
    ],
    "established": "2020",
    "nameBn": "সুনামগঞ্জ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (SSTU)",
    "nameEn": "Sunamganj Science and Technology University (SSTU)",
    "summaryBn": "2020 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: সুনামগঞ্জ (Shantiganj, Sunamganj)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2020, located in Shantiganj, Sunamganj (Sunamganj).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2020 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Shantiganj, Sunamganj (সুনামগঞ্জ জেলা); সংক্ষিপ্ত নাম: SSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2020; Type: Public Science & Technology University",
      "Location: Shantiganj, Sunamganj (Sunamganj District); Acronym: SSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-lstu",
    "category": "university",
    "districtKey": "lakshmipur",
    "coords": [
      90.8306,
      22.8355
    ],
    "established": "2020",
    "nameBn": "লক্ষ্মীপুর বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (LSTU)",
    "nameEn": "Lakshmipur Science and Technology University (LSTU)",
    "summaryBn": "2020 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: লক্ষ্মীপুর (Lakshmipur Sadar, Lakshmipur)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2020, located in Lakshmipur Sadar, Lakshmipur (Lakshmipur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2020 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Lakshmipur Sadar, Lakshmipur (লক্ষ্মীপুর জেলা); সংক্ষিপ্ত নাম: LSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2020; Type: Public Science & Technology University",
      "Location: Lakshmipur Sadar, Lakshmipur (Lakshmipur District); Acronym: LSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-prstu",
    "category": "university",
    "districtKey": "pirojpur",
    "coords": [
      90.0228,
      22.5131
    ],
    "established": "2022",
    "nameBn": "পিরোজপুর বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (PrSTU)",
    "nameEn": "Pirojpur Science and Technology University (PrSTU)",
    "summaryBn": "2022 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: পিরোজপুর (Pirojpur Sadar, Pirojpur)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2022, located in Pirojpur Sadar, Pirojpur (Pirojpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2022 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Pirojpur Sadar, Pirojpur (পিরোজপুর জেলা); সংক্ষিপ্ত নাম: PrSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2022; Type: Public Science & Technology University",
      "Location: Pirojpur Sadar, Pirojpur (Pirojpur District); Acronym: PrSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-saust",
    "category": "university",
    "districtKey": "satkhira",
    "coords": [
      89.1279,
      22.2995
    ],
    "established": "2023",
    "nameBn": "সাতক্ষীরা বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (SaUST)",
    "nameEn": "Satkhira University of Science and Technology (SaUST)",
    "summaryBn": "2023 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: সাতক্ষীরা (Satkhira Sadar, Satkhira)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2023, located in Satkhira Sadar, Satkhira (Satkhira).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2023 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Satkhira Sadar, Satkhira (সাতক্ষীরা জেলা); সংক্ষিপ্ত নাম: SaUST",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2023; Type: Public Science & Technology University",
      "Location: Satkhira Sadar, Satkhira (Satkhira District); Acronym: SaUST",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-ngstu",
    "category": "university",
    "districtKey": "narayanganj",
    "coords": [
      90.5851,
      23.7144
    ],
    "established": "2023",
    "nameBn": "নারায়ণগঞ্জ বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয় (NGSTU)",
    "nameEn": "Narayanganj Science and Technology University (NGSTU)",
    "summaryBn": "2023 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: নারায়ণগঞ্জ (Narayanganj Sadar, Narayanganj)।",
    "summaryEn": "Public Science & Technology University of Bangladesh established in 2023, located in Narayanganj Sadar, Narayanganj (Narayanganj).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2023 সাল; ধরন: সরকারি বিজ্ঞান ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Narayanganj Sadar, Narayanganj (নারায়ণগঞ্জ জেলা); সংক্ষিপ্ত নাম: NGSTU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2023; Type: Public Science & Technology University",
      "Location: Narayanganj Sadar, Narayanganj (Narayanganj District); Acronym: NGSTU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-mist",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.2408,
      23.874
    ],
    "established": "1998",
    "nameBn": "মিলিটারি ইনস্টিটিউট অব সায়েন্স অ্যান্ড টেকনোলজি (MIST)",
    "nameEn": "Military Institute of Science and Technology (MIST)",
    "summaryBn": "1998 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Mirpur Cantonment, Dhaka)।",
    "summaryEn": "Public Engineering & Technology University of Bangladesh established in 1998, located in Mirpur Cantonment, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1998 সাল; ধরন: সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Mirpur Cantonment, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: MIST",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1998; Type: Public Engineering & Technology University",
      "Location: Mirpur Cantonment, Dhaka (Dhaka District); Acronym: MIST",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-ruet",
    "category": "university",
    "districtKey": "rajshahi",
    "coords": [
      88.642,
      24.4471
    ],
    "established": "2003",
    "nameBn": "রাজশাহী প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (RUET)",
    "nameEn": "Rajshahi University of Engineering & Technology (RUET)",
    "summaryBn": "2003 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: রাজশাহী (Motihar, Rajshahi)।",
    "summaryEn": "Public Engineering & Technology University of Bangladesh established in 2003, located in Motihar, Rajshahi (Rajshahi).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2003 সাল; ধরন: সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Motihar, Rajshahi (রাজশাহী জেলা); সংক্ষিপ্ত নাম: RUET",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2003; Type: Public Engineering & Technology University",
      "Location: Motihar, Rajshahi (Rajshahi District); Acronym: RUET",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-kuet",
    "category": "university",
    "districtKey": "khulna",
    "coords": [
      89.4603,
      22.3902
    ],
    "established": "2003",
    "nameBn": "খুলনা প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (KUET)",
    "nameEn": "Khulna University of Engineering & Technology (KUET)",
    "summaryBn": "2003 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: খুলনা (Fulbari Gate, Khulna)।",
    "summaryEn": "Public Engineering & Technology University of Bangladesh established in 2003, located in Fulbari Gate, Khulna (Khulna).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2003 সাল; ধরন: সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Fulbari Gate, Khulna (খুলনা জেলা); সংক্ষিপ্ত নাম: KUET",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2003; Type: Public Engineering & Technology University",
      "Location: Fulbari Gate, Khulna (Khulna District); Acronym: KUET",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-cuet",
    "category": "university",
    "districtKey": "chattogram",
    "coords": [
      91.7557,
      22.3538
    ],
    "established": "2003",
    "nameBn": "চট্টগ্রাম প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (CUET)",
    "nameEn": "Chittagong University of Engineering & Technology (CUET)",
    "summaryBn": "2003 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: চট্টগ্রাম (Raozan, Chittagong)।",
    "summaryEn": "Public Engineering & Technology University of Bangladesh established in 2003, located in Raozan, Chittagong (Chattogram).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2003 সাল; ধরন: সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Raozan, Chittagong (চট্টগ্রাম জেলা); সংক্ষিপ্ত নাম: CUET",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2003; Type: Public Engineering & Technology University",
      "Location: Raozan, Chittagong (Chattogram District); Acronym: CUET",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-duet",
    "category": "university",
    "districtKey": "gazipur",
    "coords": [
      90.425,
      24.0924
    ],
    "established": "2003",
    "nameBn": "ঢাকা প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয় (DUET)",
    "nameEn": "Dhaka University of Engineering & Technology (DUET)",
    "summaryBn": "2003 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়, অবস্থান: গাজীপুর (Joydebpur, Gazipur)।",
    "summaryEn": "Public Engineering & Technology University of Bangladesh established in 2003, located in Joydebpur, Gazipur (Gazipur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2003 সাল; ধরন: সরকারি প্রকৌশল ও প্রযুক্তি বিশ্ববিদ্যালয়",
      "অবস্থান: Joydebpur, Gazipur (গাজীপুর জেলা); সংক্ষিপ্ত নাম: DUET",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2003; Type: Public Engineering & Technology University",
      "Location: Joydebpur, Gazipur (Gazipur District); Acronym: DUET",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-bau",
    "category": "university",
    "districtKey": "mymensingh",
    "coords": [
      90.4159,
      24.7594
    ],
    "established": "1961",
    "nameBn": "বাংলাদেশ কৃষি বিশ্ববিদ্যালয় (BAU)",
    "nameEn": "Bangladesh Agricultural University (BAU)",
    "summaryBn": "1961 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: ময়মনসিংহ (Kotwali, Mymensingh)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 1961, located in Kotwali, Mymensingh (Mymensingh).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1961 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Kotwali, Mymensingh (ময়মনসিংহ জেলা); সংক্ষিপ্ত নাম: BAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1961; Type: Public Agricultural University",
      "Location: Kotwali, Mymensingh (Mymensingh District); Acronym: BAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-gau",
    "category": "university",
    "districtKey": "gazipur",
    "coords": [
      90.4393,
      24.1244
    ],
    "established": "1998",
    "nameBn": "গাজীপুর কৃষি বিশ্ববিদ্যালয় (GAU)",
    "nameEn": "Gazipur Agricultural University (GAU)",
    "summaryBn": "1998 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: গাজীপুর (Salna, Gazipur)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 1998, located in Salna, Gazipur (Gazipur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1998 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Salna, Gazipur (গাজীপুর জেলা); সংক্ষিপ্ত নাম: GAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1998; Type: Public Agricultural University",
      "Location: Salna, Gazipur (Gazipur District); Acronym: GAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-sbau",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.1623,
      23.8053
    ],
    "established": "2001",
    "nameBn": "শেরেবাংলা কৃষি বিশ্ববিদ্যালয় (SAU)",
    "nameEn": "Sher-e-Bangla Agricultural University (SBAU)",
    "summaryBn": "2001 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Sher e Bangla Nagar, Dhaka)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 2001, located in Sher e Bangla Nagar, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2001 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Sher e Bangla Nagar, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: SBAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2001; Type: Public Agricultural University",
      "Location: Sher e Bangla Nagar, Dhaka (Dhaka District); Acronym: SBAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-sau",
    "category": "university",
    "districtKey": "sylhet",
    "coords": [
      92.0784,
      24.9222
    ],
    "established": "2006",
    "nameBn": "সিলেট কৃষি বিশ্ববিদ্যালয় (SAU)",
    "nameEn": "Sylhet Agricultural University (SAU)",
    "summaryBn": "2006 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: সিলেট (Uttar Baluchar, Sylhet)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 2006, located in Uttar Baluchar, Sylhet (Sylhet).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2006 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Uttar Baluchar, Sylhet (সিলেট জেলা); সংক্ষিপ্ত নাম: SAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2006; Type: Public Agricultural University",
      "Location: Uttar Baluchar, Sylhet (Sylhet District); Acronym: SAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-kau",
    "category": "university",
    "districtKey": "khulna",
    "coords": [
      89.4355,
      22.3348
    ],
    "established": "2019",
    "nameBn": "খুলনা কৃষি বিশ্ববিদ্যালয় (KAU)",
    "nameEn": "Khulna Agricultural University (KAU)",
    "summaryBn": "2019 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: খুলনা (Daulatpur, Khulna)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 2019, located in Daulatpur, Khulna (Khulna).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2019 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Daulatpur, Khulna (খুলনা জেলা); সংক্ষিপ্ত নাম: KAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2019; Type: Public Agricultural University",
      "Location: Daulatpur, Khulna (Khulna District); Acronym: KAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-hau",
    "category": "university",
    "districtKey": "habiganj",
    "coords": [
      91.4088,
      24.3353
    ],
    "established": "2020",
    "nameBn": "হবিগঞ্জ কৃষি বিশ্ববিদ্যালয় (HAU)",
    "nameEn": "Habiganj Agricultural University (HAU)",
    "summaryBn": "2020 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: হবিগঞ্জ (Habiganj Sadar, Habiganj)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 2020, located in Habiganj Sadar, Habiganj (Habiganj).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2020 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Habiganj Sadar, Habiganj (হবিগঞ্জ জেলা); সংক্ষিপ্ত নাম: HAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2020; Type: Public Agricultural University",
      "Location: Habiganj Sadar, Habiganj (Habiganj District); Acronym: HAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-kuriau",
    "category": "university",
    "districtKey": "kurigram",
    "coords": [
      89.6694,
      25.8055
    ],
    "established": "2022",
    "nameBn": "কুড়িগ্রাম কৃষি বিশ্ববিদ্যালয় (KuriAU)",
    "nameEn": "Kurigram Agricultural University (KuriAU)",
    "summaryBn": "2022 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: কুড়িগ্রাম (Kurigram, Kurigram)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 2022, located in Kurigram, Kurigram (Kurigram).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2022 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Kurigram, Kurigram (কুড়িগ্রাম জেলা); সংক্ষিপ্ত নাম: KuriAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2022; Type: Public Agricultural University",
      "Location: Kurigram, Kurigram (Kurigram District); Acronym: KuriAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-shau",
    "category": "university",
    "districtKey": "shariatpur",
    "coords": [
      90.4147,
      23.2278
    ],
    "established": "2021",
    "nameBn": "শরীয়তপুর কৃষি বিশ্ববিদ্যালয় (ShAU)",
    "nameEn": "Shariatpur Agriculture University (ShAU)",
    "summaryBn": "2021 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি কৃষি বিশ্ববিদ্যালয়, অবস্থান: শরীয়তপুর (Shariatpur, Sadar Shariatpur)।",
    "summaryEn": "Public Agricultural University of Bangladesh established in 2021, located in Shariatpur, Sadar Shariatpur (Shariatpur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2021 সাল; ধরন: সরকারি কৃষি বিশ্ববিদ্যালয়",
      "অবস্থান: Shariatpur, Sadar Shariatpur (শরীয়তপুর জেলা); সংক্ষিপ্ত নাম: ShAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2021; Type: Public Agricultural University",
      "Location: Shariatpur, Sadar Shariatpur (Shariatpur District); Acronym: ShAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-bmu-medical",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.1957,
      23.6993
    ],
    "established": "1998",
    "nameBn": "বাংলাদেশ মেডিকেল বিশ্ববিদ্যালয় (পূর্বনাম BSMMU)",
    "nameEn": "Bangladesh Medical University (BMU)",
    "summaryBn": "1998 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি মেডিকেল বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Shabagh, Dhaka)।",
    "summaryEn": "Public Medical University of Bangladesh established in 1998, located in Shabagh, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1998 সাল; ধরন: সরকারি মেডিকেল বিশ্ববিদ্যালয়",
      "অবস্থান: Shabagh, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: BMU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1998; Type: Public Medical University",
      "Location: Shabagh, Dhaka (Dhaka District); Acronym: BMU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-cmu",
    "category": "university",
    "districtKey": "chattogram",
    "coords": [
      91.831,
      22.3836
    ],
    "established": "2016",
    "nameBn": "চট্টগ্রাম মেডিকেল বিশ্ববিদ্যালয় (CMU)",
    "nameEn": "Chittagong Medical University (CMU)",
    "summaryBn": "2016 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি মেডিকেল বিশ্ববিদ্যালয়, অবস্থান: চট্টগ্রাম (Salimpur, Chittagong)।",
    "summaryEn": "Public Medical University of Bangladesh established in 2016, located in Salimpur, Chittagong (Chattogram).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2016 সাল; ধরন: সরকারি মেডিকেল বিশ্ববিদ্যালয়",
      "অবস্থান: Salimpur, Chittagong (চট্টগ্রাম জেলা); সংক্ষিপ্ত নাম: CMU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2016; Type: Public Medical University",
      "Location: Salimpur, Chittagong (Chattogram District); Acronym: CMU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-rmu",
    "category": "university",
    "districtKey": "rajshahi",
    "coords": [
      88.5947,
      24.4521
    ],
    "established": "2017",
    "nameBn": "রাজশাহী মেডিকেল বিশ্ববিদ্যালয় (RMU)",
    "nameEn": "Rajshahi Medical University (RMU)",
    "summaryBn": "2017 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি মেডিকেল বিশ্ববিদ্যালয়, অবস্থান: রাজশাহী (Rajpara, Rajshahi)।",
    "summaryEn": "Public Medical University of Bangladesh established in 2017, located in Rajpara, Rajshahi (Rajshahi).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2017 সাল; ধরন: সরকারি মেডিকেল বিশ্ববিদ্যালয়",
      "অবস্থান: Rajpara, Rajshahi (রাজশাহী জেলা); সংক্ষিপ্ত নাম: RMU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2017; Type: Public Medical University",
      "Location: Rajpara, Rajshahi (Rajshahi District); Acronym: RMU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-smu",
    "category": "university",
    "districtKey": "sylhet",
    "coords": [
      92.0312,
      24.9272
    ],
    "established": "2018",
    "nameBn": "সিলেট মেডিকেল বিশ্ববিদ্যালয় (SMU)",
    "nameEn": "Sylhet Medical University (SMU)",
    "summaryBn": "2018 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি মেডিকেল বিশ্ববিদ্যালয়, অবস্থান: সিলেট (Ambarkhana, Sylhet)।",
    "summaryEn": "Public Medical University of Bangladesh established in 2018, located in Ambarkhana, Sylhet (Sylhet).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2018 সাল; ধরন: সরকারি মেডিকেল বিশ্ববিদ্যালয়",
      "অবস্থান: Ambarkhana, Sylhet (সিলেট জেলা); সংক্ষিপ্ত নাম: SMU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2018; Type: Public Medical University",
      "Location: Ambarkhana, Sylhet (Sylhet District); Acronym: SMU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-kmu",
    "category": "university",
    "districtKey": "khulna",
    "coords": [
      89.4854,
      22.2837
    ],
    "established": "2020",
    "nameBn": "খুলনা মেডিকেল বিশ্ববিদ্যালয় (KMU)",
    "nameEn": "Khulna Medical University (KMU)",
    "summaryBn": "2020 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি মেডিকেল বিশ্ববিদ্যালয়, অবস্থান: খুলনা (Labanchara, Khulna)।",
    "summaryEn": "Public Medical University of Bangladesh established in 2020, located in Labanchara, Khulna (Khulna).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2020 সাল; ধরন: সরকারি মেডিকেল বিশ্ববিদ্যালয়",
      "অবস্থান: Labanchara, Khulna (খুলনা জেলা); সংক্ষিপ্ত নাম: KMU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2020; Type: Public Medical University",
      "Location: Labanchara, Khulna (Khulna District); Acronym: KMU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-cvasu",
    "category": "university",
    "districtKey": "chattogram",
    "coords": [
      91.8335,
      22.473
    ],
    "established": "2006",
    "nameBn": "চট্টগ্রাম ভেটেরিনারি ও অ্যানিম্যাল সাইন্সেস বিশ্ববিদ্যালয় (CVASU)",
    "nameEn": "Chittagong Veterinary and Animal Sciences University (CVASU)",
    "summaryBn": "2006 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: চট্টগ্রাম (Jhautala, Chittagong)।",
    "summaryEn": "Public Specialized Public University of Bangladesh established in 2006, located in Jhautala, Chittagong (Chattogram).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2006 সাল; ধরন: সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Jhautala, Chittagong (চট্টগ্রাম জেলা); সংক্ষিপ্ত নাম: CVASU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2006; Type: Public Specialized Public University",
      "Location: Jhautala, Chittagong (Chattogram District); Acronym: CVASU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-butex",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.3123,
      23.6863
    ],
    "established": "2010",
    "nameBn": "বাংলাদেশ টেক্সটাইল বিশ্ববিদ্যালয় (BUTEX)",
    "nameEn": "Bangladesh University of Textiles (BUTEX)",
    "summaryBn": "2010 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Tejgaon Industrial area, Dhaka)।",
    "summaryEn": "Public Specialized Public University of Bangladesh established in 2010, located in Tejgaon Industrial area, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2010 সাল; ধরন: সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Tejgaon Industrial area, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: BUTEX",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2010; Type: Public Specialized Public University",
      "Location: Tejgaon Industrial area, Dhaka (Dhaka District); Acronym: BUTEX",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-bmu-maritime",
    "category": "university",
    "districtKey": "chattogram",
    "coords": [
      91.7456,
      22.5142
    ],
    "established": "2013",
    "nameBn": "বাংলাদেশ মেরিটাইম বিশ্ববিদ্যালয় (BMU)",
    "nameEn": "Bangladesh Maritime University (BMU)",
    "summaryBn": "2013 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: চট্টগ্রাম (Sitakunda, Chittagong)।",
    "summaryEn": "Public Specialized Public University of Bangladesh established in 2013, located in Sitakunda, Chittagong (Chattogram).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2013 সাল; ধরন: সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Sitakunda, Chittagong (চট্টগ্রাম জেলা); সংক্ষিপ্ত নাম: BMU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2013; Type: Public Specialized Public University",
      "Location: Sitakunda, Chittagong (Chattogram District); Acronym: BMU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-uftb",
    "category": "university",
    "districtKey": "gazipur",
    "coords": [
      90.3921,
      24.1294
    ],
    "established": "2018",
    "nameBn": "ইউনিভার্সিটি অব ফ্রন্টিয়ার টেকনোলজি, বাংলাদেশ (UFTB)",
    "nameEn": "University of Frontier Technology, Bangladesh (UFTB)",
    "summaryBn": "2018 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: গাজীপুর (Kaliakoir, Gazipur)।",
    "summaryEn": "Public Specialized Public University of Bangladesh established in 2018, located in Kaliakoir, Gazipur (Gazipur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2018 সাল; ধরন: সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Kaliakoir, Gazipur (গাজীপুর জেলা); সংক্ষিপ্ত নাম: UFTB",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2018; Type: Public Specialized Public University",
      "Location: Kaliakoir, Gazipur (Gazipur District); Acronym: UFTB",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-aaub",
    "category": "university",
    "districtKey": "lalmonirhat",
    "coords": [
      89.2388,
      26.1221
    ],
    "established": "2019",
    "nameBn": "এভিয়েশন অ্যান্ড অ্যারোস্পেস বিশ্ববিদ্যালয়, বাংলাদেশ (AAUB)",
    "nameEn": "Aviation and Aerospace University Bangladesh (AAUB)",
    "summaryBn": "2019 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: লালমনিরহাট (Lalmonirhat Sadar, Lalmonirhat)।",
    "summaryEn": "Public Specialized Public University of Bangladesh established in 2019, located in Lalmonirhat Sadar, Lalmonirhat (Lalmonirhat).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2019 সাল; ধরন: সরকারি বিশেষায়িত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Lalmonirhat Sadar, Lalmonirhat (লালমনিরহাট জেলা); সংক্ষিপ্ত নাম: AAUB",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2019; Type: Public Specialized Public University",
      "Location: Lalmonirhat Sadar, Lalmonirhat (Lalmonirhat District); Acronym: AAUB",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-nu",
    "category": "university",
    "districtKey": "gazipur",
    "coords": [
      90.3673,
      24.074
    ],
    "established": "1992",
    "nameBn": "জাতীয় বিশ্ববিদ্যালয়, বাংলাদেশ (NU)",
    "nameEn": "National University Bangladesh (NU)",
    "summaryBn": "1992 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: গাজীপুর (Joydebpur, Gazipur)।",
    "summaryEn": "Public Off-Campus / Affiliating Public University of Bangladesh established in 1992, located in Joydebpur, Gazipur (Gazipur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1992 সাল; ধরন: সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Joydebpur, Gazipur (গাজীপুর জেলা); সংক্ষিপ্ত নাম: NU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1992; Type: Public Off-Campus / Affiliating Public University",
      "Location: Joydebpur, Gazipur (Gazipur District); Acronym: NU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-bou",
    "category": "university",
    "districtKey": "gazipur",
    "coords": [
      90.4172,
      24.0229
    ],
    "established": "1992",
    "nameBn": "বাংলাদেশ উন্মুক্ত বিশ্ববিদ্যালয় (BOU)",
    "nameEn": "Bangladesh Open University (BOU)",
    "summaryBn": "1992 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: গাজীপুর (Joydebpur, Gazipur)।",
    "summaryEn": "Public Off-Campus / Affiliating Public University of Bangladesh established in 1992, located in Joydebpur, Gazipur (Gazipur).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 1992 সাল; ধরন: সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Joydebpur, Gazipur (গাজীপুর জেলা); সংক্ষিপ্ত নাম: BOU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 1992; Type: Public Off-Campus / Affiliating Public University",
      "Location: Joydebpur, Gazipur (Gazipur District); Acronym: BOU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-iau",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.3745,
      23.7929
    ],
    "established": "2013",
    "nameBn": "ইসলামি আরবি বিশ্ববিদ্যালয় (IAU)",
    "nameEn": "Islamic Arabic University (IAU)",
    "summaryBn": "2013 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Mohammadpur, Dhaka)।",
    "summaryEn": "Public Off-Campus / Affiliating Public University of Bangladesh established in 2013, located in Mohammadpur, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2013 সাল; ধরন: সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Mohammadpur, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: IAU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2013; Type: Public Off-Campus / Affiliating Public University",
      "Location: Mohammadpur, Dhaka (Dhaka District); Acronym: IAU",
      "UGC-approved public university of Bangladesh"
    ]
  },
  {
    "id": "uni-dcu",
    "category": "university",
    "districtKey": "dhaka",
    "coords": [
      90.299,
      23.8976
    ],
    "established": "2026",
    "nameBn": "ঢাকা সেন্ট্রাল ইউনিভার্সিটি (DCU)",
    "nameEn": "Dhaka Central University (DCU)",
    "summaryBn": "2026 সালে প্রতিষ্ঠিত বাংলাদেশের সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়, অবস্থান: ঢাকা (Agargaon, Dhaka)।",
    "summaryEn": "Public Off-Campus / Affiliating Public University of Bangladesh established in 2026, located in Agargaon, Dhaka (Dhaka).",
    "examPointsBn": [
      "প্রতিষ্ঠাকাল: 2026 সাল; ধরন: সরকারি অফ-ক্যাম্পাস / অধিভুক্ত পাবলিক বিশ্ববিদ্যালয়",
      "অবস্থান: Agargaon, Dhaka (ঢাকা জেলা); সংক্ষিপ্ত নাম: DCU",
      "বাংলাদেশ বিশ্ববিদ্যালয় মঞ্জুরি কমিশন (UGC) অনুমোদিত পাবলিক বিশ্ববিদ্যালয়"
    ],
    "examPointsEn": [
      "Established: 2026; Type: Public Off-Campus / Affiliating Public University",
      "Location: Agargaon, Dhaka (Dhaka District); Acronym: DCU",
      "UGC-approved public university of Bangladesh"
    ]
  }
];
