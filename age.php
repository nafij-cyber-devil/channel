<?php
header('Content-Type: application/json');

// Get the date of birth from query parameters
$dob = isset($_GET['dob']) ? $_GET['dob'] : null;

if (!$dob) {
    http_response_code(400);
    echo json_encode(['error' => 'Date of birth (dob) parameter is required']);
    exit;
}

// Validate date format
if (!DateTime::createFromFormat('Y-m-d', $dob)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format. Please use YYYY-MM-DD']);
    exit;
}

$startTime = microtime(true);
$birthDate = new DateTime($dob);
$now = new DateTime();

if ($birthDate > $now) {
    http_response_code(400);
    echo json_encode(['error' => 'Date of birth cannot be in the future']);
    exit;
}

// Age categories with ranges
$ageCategories = [
    ['min' => 0, 'max' => 1, 'category' => "Infant", 'message' => "Welcome to the world!"],
    ['min' => 1, 'max' => 3, 'category' => "Toddler", 'message' => "Exploring the world one step at a time."],
    ['min' => 3, 'max' => 12, 'category' => "Child", 'message' => "Learning and growing every day."],
    ['min' => 12, 'max' => 18, 'category' => "Teenager", 'message' => "Discovering your identity and passions."],
    ['min' => 18, 'max' => 30, 'category' => "Young Adult", 'message' => "Building your path towards success."],
    ['min' => 30, 'max' => 45, 'category' => "Adult", 'message' => "In your prime years of productivity."],
    ['min' => 45, 'max' => 60, 'category' => "Middle-aged", 'message' => "Wisdom and experience define you."],
    ['min' => 60, 'max' => 120, 'category' => "Senior", 'message' => "Sharing a lifetime of knowledge."]
];

// Generation ranges
$generations = [
    ['min' => 1928, 'max' => 1945, 'name' => "Silent Generation"],
    ['min' => 1946, 'max' => 1964, 'name' => "Baby Boomer"],
    ['min' => 1965, 'max' => 1980, 'name' => "Gen X"],
    ['min' => 1981, 'max' => 1996, 'name' => "Millennial"],
    ['min' => 1997, 'max' => 2012, 'name' => "Gen Z"],
    ['min' => 2013, 'max' => 2025, 'name' => "Gen Alpha"]
];

// Zodiac signs with dates and meanings
$zodiacSigns = [
    ['name' => "Aries", 'start' => "03-21", 'end' => "04-19", 'meaning' => "Adventurous, energetic, courageous."],
    ['name' => "Taurus", 'start' => "04-20", 'end' => "05-20", 'meaning' => "Patient, reliable, warmhearted."],
    ['name' => "Gemini", 'start' => "05-21", 'end' => "06-20", 'meaning' => "Adaptable, outgoing, intelligent."],
    ['name' => "Cancer", 'start' => "06-21", 'end' => "07-22", 'meaning' => "Emotional, loving, intuitive."],
    ['name' => "Leo", 'start' => "07-23", 'end' => "08-22", 'meaning' => "Generous, warmhearted, creative."],
    ['name' => "Virgo", 'start' => "08-23", 'end' => "09-22", 'meaning' => "Loyal, analytical, kind."],
    ['name' => "Libra", 'start' => "09-23", 'end' => "10-22", 'meaning' => "Diplomatic, fair-minded, social."],
    ['name' => "Scorpio", 'start' => "10-23", 'end' => "11-21", 'meaning' => "Determined, passionate, magnetic."],
    ['name' => "Sagittarius", 'start' => "11-22", 'end' => "12-21", 'meaning' => "Optimistic, freedom-loving, humorous."],
    ['name' => "Capricorn", 'start' => "12-22", 'end' => "01-19", 'meaning' => "Practical, ambitious, disciplined."],
    ['name' => "Aquarius", 'start' => "01-20", 'end' => "02-18", 'meaning' => "Independent, innovative, free-spirited."],
    ['name' => "Pisces", 'start' => "02-19", 'end' => "03-20", 'meaning' => "Compassionate, artistic, intuitive."]
];

// Hobby suggestions by age group
$hobbySuggestions = [
    ['minAge' => 0, 'maxAge' => 5, 'hobbies' => ["Coloring", "Storytime", "Playing with blocks"]],
    ['minAge' => 6, 'maxAge' => 12, 'hobbies' => ["Reading", "Drawing", "Sports", "Music"]],
    ['minAge' => 13, 'maxAge' => 19, 'hobbies' => ["Photography", "Writing", "Gaming", "Learning an instrument"]],
    ['minAge' => 20, 'maxAge' => 30, 'hobbies' => ["Traveling", "Fitness", "Cooking", "Blogging"]],
    ['minAge' => 31, 'maxAge' => 45, 'hobbies' => ["Gardening", "DIY projects", "Yoga", "Wine tasting"]],
    ['minAge' => 46, 'maxAge' => 60, 'hobbies' => ["Golf", "Chess", "Painting", "Genealogy"]],
    ['minAge' => 61, 'maxAge' => 120, 'hobbies' => ["Walking", "Bird watching", "Knitting", "Reading"]]
];

// Calculate age in years and months
$ageInterval = $birthDate->diff($now);
$years = $ageInterval->y;
$months = $ageInterval->m;
$ageString = "$years yr $months month";

// Determine age category
$categoryInfo = ['category' => "Unknown", 'message' => "Age out of range"];
foreach ($ageCategories as $category) {
    if ($years >= $category['min'] && $years < $category['max']) {
        $categoryInfo = $category;
        break;
    }
}

// Determine generation
$birthYear = $birthDate->format('Y');
$generationInfo = ['name' => "Unknown Generation"];
foreach ($generations as $generation) {
    if ($birthYear >= $generation['min'] && $birthYear <= $generation['max']) {
        $generationInfo = $generation;
        break;
    }
}

// Determine zodiac sign
$monthDay = $birthDate->format('m-d');
$zodiacInfo = ['name' => "Unknown", 'meaning' => "Zodiac sign not found"];
foreach ($zodiacSigns as $sign) {
    if ($monthDay >= $sign['start'] && $monthDay <= $sign['end']) {
        $zodiacInfo = $sign;
        break;
    }
}

// Calculate birthday countdown
$nextBirthday = new DateTime($birthDate->format('m-d') . '-' . $now->format('Y'));
if ($nextBirthday < $now) {
    $nextBirthday->modify('+1 year');
}
$daysUntilBirthday = $now->diff($nextBirthday)->days;
$countdownString = $daysUntilBirthday > 0 ? "$daysUntilBirthday days left" : "Today is your birthday!";

// Get hobby suggestion
$ageForHobby = $years;
$hobbyGroup = ['hobbies' => ["Unknown"]];
foreach ($hobbySuggestions as $group) {
    if ($ageForHobby >= $group['minAge'] && $ageForHobby <= $group['maxAge']) {
        $hobbyGroup = $group;
        break;
    }
}
$randomHobby = $hobbyGroup['hobbies'][array_rand($hobbyGroup['hobbies'])];

// Calculate response time
$responseTime = round((microtime(true) - $startTime) * 1000) . ' ms';

// Prepare response
$response = [
    'dob' => $dob,
    'age' => $ageString,
    'category' => $categoryInfo['category'],
    'message' => $categoryInfo['message'],
    'generation' => $generationInfo['name'],
    'zodiac_sign' => $zodiacInfo['name'],
    'zodiac_meaning' => $zodiacInfo['meaning'],
    'birthday_countdown' => $countdownString,
    'hobby_suggestion' => $randomHobby,
    'response_time' => $responseTime
];

echo json_encode($response);
?>