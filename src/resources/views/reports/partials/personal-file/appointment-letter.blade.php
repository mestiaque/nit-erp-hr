

<div style="text-align:center; margin-bottom:16px">
	<h2>{{ general()->title ?? '' }}</h2>
	<div>{{ general()->address_one ?? '' }}</div>
	<h3 style="margin-top:10px;">নিয়োগ পত্র (Appointment Letter)</h3>
</div>
<table style="width:100%; margin-bottom:10px; font-size:15px">
	<tr>
		<td>শ্রমিকের নাম : {{ $employee->name ?? '' }}</td>
		<td>পিতার নাম : {{ $employee->father_name ?? '' }}</td>
	</tr>
	<tr>
		<td>স্বামী/স্ত্রীর নাম : {{ $employee->spouse_name ?? '' }}</td>
		<td>মাতার নাম : {{ $employee->mother_name ?? '' }}</td>
	</tr>
	<tr>
		<td>সেকশন : {{ $employee->section ?? '' }}</td>
		<td>পদবী : {{ $employee->designation->name ?? $employee->designation_name ?? '' }}</td>
	</tr>
	<tr>
		<td>যোগদানের তারিখ : {{ $fmtDate($employee->joining_date ?? '') }}</td>
		<td>কাজের ধরন : {{ $employee->employee_type ?? '' }}</td>
	</tr>
	<tr>
		<td colspan="">বর্তমান ঠিকানা : {{ $employee->present_address ?? $employee->address ?? '' }}</td>
        <td>কার্ড নং : {{ $employee->employee_id ?? $employee->id ?? '' }}</td>
	</tr>
	<tr>
		<td colspan="2">স্থায়ী ঠিকানা : {{ $employee->permanent_address ?? '' }}</td>
	</tr>
</table>

<div style="margin-bottom:12px; font-size:15px;">
	আপনার আবেদন, সাক্ষাৎকার এবং যোগ্যতা যাচাইয়ের পরিপ্রেক্ষিতে আপনাকে {{ $fmtDate($employee->joining_date ?? '') }} ইং তারিখ হইতে {{ $employee->designation->name ?? $employee->designation_name ?? '' }} পদে {{ $employee->grade ?? '___' }} নং গ্রেডে নম্নলিখিত শর্তসাপেক্ষে নিয়োগ প্রদান করিতেছি। নিয়োগ প্রাপ্তির তিন মাস প্রাথমিক পর্যায় হিসাবে গণ্য হবে। যদি দেখা যায় যে নিয়োগ প্রাপ্তির তিন মাস প্রাথমিক পর্যায়ে আপনি সম্পূর্ণ যোগ্যতা অর্জন করিতে পারেন নাই, তখন ইহা আর ও তিন মাস পর্যন্ত বর্ধিত করা হবে। এই সময়ের পর আপনাকে স্থায়ী শ্রমিক হিসাবে গণ্য করা হবে এবং ইহার জন্য কোন প্রকার চিঠি দেওয়া হবে না।
</div>

<table style="width:100%; margin-bottom:10px; font-size:15px">
	<tr><td colspan="2"><b>১। বেতন ও ভাতাঃ</b></td></tr>
	@php
		// Get gross from employee
		$gross = $employee->gross_salary ?? ($employee->basic_salary + $employee->house_rent + $employee->medical_allowance + $employee->transport_allowance + $employee->food_allowance ?? null);
		// Get Medical, Transport, Food (MTF) from salary key (if available)
		$medical = $salaryKey->medical ?? 0;
		$transport = $salaryKey->transport ?? 0;
		$food = $salaryKey->lunch ?? 0;
		$mtf = $medical + $transport + $food;
		// Calculate Basic and House
		$basic = $gross && $mtf ? ($gross - $mtf) / 1.5 : null;
		$house = $basic ? $basic / 2 : null;
		// OT Rate from factory (if available)
		$ot_rate = $factory->ot_rate ?? $employee->ot_rate ?? null;
	@endphp
	<tr><td>ক। মাসিক মূল মঞ্জুরী (Basic)</td><td>টাকা {{ isset($basic) ? number_format($basic, 2) : '___' }}/=</td></tr>
	<tr><td>খ। মাসিক বাড়ী ভাড়া ভাতা (House Rent)</td><td>টাকা {{ isset($house) ? number_format($house, 2) : '___' }}/=</td></tr>
	<tr><td>গ। মাসিক চিকিৎসা ভাতা (Medical)</td><td>টাকা {{ isset($medical) ? number_format($medical, 2) : '___' }}/=</td></tr>
	<tr><td>ঘ। মাসিক যাতায়াত ভাতা (Transport)</td><td>টাকা {{ isset($transport) ? number_format($transport, 2) : '___' }}/=</td></tr>
	<tr><td>ঙ। মাসিক খাদ্য ভাতা (Food)</td><td>টাকা {{ isset($food) ? number_format($food, 2) : '___' }}/=</td></tr>
	<tr><td>মাসিক সর্বমোট টাকা (Gross)</td><td>টাকা {{ isset($gross) ? number_format($gross, 2) : '___' }}/=</td></tr>
	<tr><td>প্রতি ঘন্টায় ও.টির হার (OT Rate)</td><td>টাকা {{ isset($ot_rate) ? number_format($ot_rate, 2) : '___' }}/=</td></tr>
</table>

<div style="margin-bottom:10px; font-size:15px;">
	০২। সাধারন কর্মঘন্টা এবং অতিরিক্ত কর্মঘন্টা (ও, টি): ক। ও. টি হিসাব মূল বেতন হিসাবে। ঘন্টা প্রতি মূল মজুরীর দ্বিগুন হারে হিসাব করা হয়। হিসাবটি নিম্নরূপঃ<br>
	মূল মজুরী/২০৮ × ২x মোট ওভার টাইম ঘন্টা।<br>
	খ। দৈনিক কর্মঘন্টা: ০৮ ঘন্টা প্রতিদিন ০১ ঘন্টা খাবার বিরতি।<br>
	গ। দৈনিক ও. টি ঘন্টা: প্রতিদিন ০৮ ঘন্টার অতিরিক্ত (খাবার বিরতি ব্যতিত) ও সপ্তাহে ৪৮ ঘন্টার অতিরিক্ত সময়ের কাজকে ওভার টাইম হিসাবে গন্য করা হইবে।<br>
	ঘ। বেতন/মজুরী প্রদানের সময়: প্রত্যেক মাসের বেতন / মজুরী, ওভার টাইম একত্রে প্রদান করা হয় (যাহা পরবর্তী মাসের ০৭ কর্মদিবসের মধ্যে যে কোন দিন প্রদান করা হয়)।
</div>


<div style="margin-bottom:10px; font-size:15px;">
	<b>বঙ্গ নীটওয়্যার লিমিটেড এর শ্রমিক/কর্মচারীদের চাকুরীর সাধারন নিয়মাবলী ও সুবিধাদি নিম্নে উল্লেখ করা হলঃ</b><br>
	<ol style="margin-top:10px;">
		<li><b>চাকুরীতে যোগদানঃ</b> চাকুরীতে যোগদানের দিন বা গ্রহণযোগ্য সময়ের মধ্যে নিম্নোক্ত কাগজপত্র সহ নির্ধারিত ফরম পুরন করে কারখানা অফিসে জমা দিতে হবে।
			<ul style="list-style-type: none; padding-left: 0;">
				<li><span style="font-weight:bold">ক।</span> ০৫ (পাঁচ) কপি ছবি। পাসপোর্ট সাইজ ৪ কপি, স্ট্যাম্প সাইজ-১ কপি।</li>
				<li><span style="font-weight:bold">খ।</span> চেয়ারম্যান/ওয়ার্ড কমিশনার কর্তৃক প্রদত্ত নাগরিকত্ব সনদপত্রের ফটোকপি (যদি থাকে)।</li>
				<li><span style="font-weight:bold">গ।</span> শিক্ষাগত যোগ্যতার সনদপত্র বা স্কুল পাশের সার্টিফিকেটের ফটোকপি (যদি থাকে)।</li>
				<li><span style="font-weight:bold">ঘ।</span> জন্ম নিবন্ধন/জাতীয়পরিচয় পত্রের (ভোটার আইডিকার্ড) ফটোকপি।</li>
			</ul>
			<ul style="list-style-type: none; padding-left: 0;">
				<li><span style="font-weight:bold">ক।</span> সপ্তাহে ০১ (এক) দিন সাপ্তাহিক ছুটি: (সাধারণত শুক্রবার) যাহা সরকারী কোন আদেশ বা কোম্পানীর প্রয়োজনী কারনে অনুমোদন সাপেদে পরিবর্তন হইতে পারে</li>
				<li><span style="font-weight:bold">খ।</span> উৎসব জনিত ছুটি: বৎসরে ১১ (এগার) দিন পূর্ণ মঞ্জুরী সহ এবং যাহা পরবর্তী বৎসরের সাথে যোগ হয় না।</li>
				<li><span style="font-weight:bold">গ।</span> নৈমিত্তিক ছুটি: বৎসরে ১০ (দশ) দিন পূর্ন মজুরীতে যাহা পরবর্তী বৎসরের সাথে যোগ হয় না।</li>
				<li><span style="font-weight:bold">ঘ।</span> বার্ষিক বা অর্জিত ছুটি: প্রতি আঠার কর্মদিবসের জন্য ১ (এক) দিন। এই ছুটি কেবল তাদের জন্য প্রযোজ্য যারা কমপক্ষে এক বৎসর চাকুরী পূর্ণ করিয়াছেন।</li>
				<li><span style="font-weight:bold">ঙ।</span> চিকিৎসা/পীড়া/অসুস্থতা ছুটি: বৎসরে ১৪ (চৌদ্দ) দিন পূর্ণ মজুরীতে যাহা পরবর্তী বৎসরের সাথে যোগ হয় না।</li>
				<li><span style="font-weight:bold">চ।</span> মাতৃত্ব কালীন ছুটি: ১৬ সপ্তাহ (১১২ দিন) (আইনানুগ ও নগদে) এই ছুটি কেবল সেই সব মহিলার জন্য প্রযেজ্য যাহারা অত্র কোম্পানীতে কমপক্ষে ছয় (০৬) মাস চাকুরী পূর্ণ করিয়াছেন। যাহার দুই এর অধিক সন্তান জীবিত নাই তিনিই এই সুবিধা ভোগ করিতে পারিবেন।</li>
			</ul>
			<ul style="list-style-type: none; padding-left: 0;">
				<li><span style="font-weight:bold">ক।</span>সপ্তাহে ০১ (এক) দিন সাপ্তাহিক ছুটি: (সাধারণত শুক্রবার) যাহা সরকারী কোন আদেশ বা কোম্পানীর প্রয়োজনী কারনে অনুমোদন সাপেদে পরিবর্তন হইতে পারে</li>
				<li><span style="font-weight:bold">খ।</span> উৎসব জনিত ছুটি: বৎসরে ১১ (এগার) দিন পূর্ণ মঞ্জুরী সহ এবং যাহা পরবর্তী বৎসরের সাথে যোগ হয় না।</li>
				<li><span style="font-weight:bold">গ।</span> নৈমিত্তিক ছুটি: বৎসরে ১০ (দশ) দিন পূর্ন মজুরীতে যাহা পরবর্তী বৎসরের সাথে যোগ হয় না।</li>
				<li><span style="font-weight:bold">ঘ।</span> বার্ষিক বা অর্জিত ছুটি: প্রতি আঠার কর্মদিবসের জন্য ১ (এক) দিন। এই ছুটি কেবল তাদের জন্য প্রযোজ্য যারা কমপক্ষে এক বৎসর চাকুরী পূর্ণ করিয়াছেন।</li>
				<li><span style="font-weight:bold">ঙ।</span> চিকিৎসা/পীড়া/অসুস্থতা ছুটি: বৎসরে ১৪ (চৌদ্দ) দিন পূর্ন মজুরীতে যাহা পরবর্তী বৎসরের সাথে যোগ হয় না।</li>
				<li><span style="font-weight:bold">চ।</span> মাতৃত্ব কালীন ছুটি: ১৬ সপ্তাহ (১১২ দিন) (আইনানুগ ও নগদে) এই ছুটি কেবল সেই সব মহিলার জন্য প্রযেজ্য যাহারা অত্র কোম্পানীতে কমপক্ষে ছয় (০৬) মাস চাকুরী পূর্ণ করিয়াছেন। যাহার দুই এর অধিক সন্তান জীবিত নাই তিনিই এই সুবিধা ভোগ করিতে পারিবেন।</li>
			</ul>
		</li>
		<li><b>ছুটির নিয়মাবলীঃ</b>
			<ul style="list-style-type: none; padding-left: 0;">
				<li><span style="font-weight:bold">ক।</span> কোন শ্রমিক/কর্মচারী ছুটি নিতে আগ্রহী হইলে অবশ্যই ছুটির আবেদনের নির্ধারিত ফরম পূরন করে অফিসে জমা দিতে হইবে।</li>
				<li><span style="font-weight:bold">খ।</span> ছুটির আবেদন মঞ্জুর হইলে কেবলমাত্র কোন শ্রমিক/কর্মচারী ছুটি ভোগ করিতে পারিবেন।</li>
			</ul>
		</li>
		<li><b>সুবিধাঃ</b>
			<ul style="list-style-type: none; padding-left: 0;">
				<li><span style="font-weight:bold">ক।</span> বিনা খরচে কারখানার ডাক্তার এবং নার্সের মাধ্যমে চিকিৎসা সুবিধা প্রদান করা হয়।</li>
				<li><span style="font-weight:bold">খ।</span> শ্রমিক/কর্মচারীর জন্য গ্রুপ ইনসুরেন্স এর ব্যবস্থা আছে।</li>
			</ul>
		</li>
		<li><b>"বাংলাদেশ শ্রম আইন ২০০৬ অনুসারে ধারা ২৩ (৪) মোতাবেক নিম্নোক্তকাজ করা অসদাচরণ" বলিয়া গণ্য হইবে যথাঃ</b>
			<ul style="list-style-type: none; padding-left: 0;">
				<li><span style="font-weight:bold">ক।</span> উপরস্থের কোন আইন সংগত বা যুক্তি সংগত আদেশ মানার ক্ষেত্রে এককভাবে বা অন্যের সঙ্গে সংঘবদ্ধ হইয়া ইচ্ছাকৃতভাবে অবাধ্যতা।</li>
				<li><span style="font-weight:bold">খ।</span> মালিকের ব্যবসা বা সম্পত্তি চুরি, প্রতারনা বা অসাধুতা:</li>
				<li><span style="font-weight:bold">গ।</span> মালিকের অধীনে তাঁহার বা অন্য কোন শ্রমিকের চাকুরী সংক্রান্ত ব্যাপারে ঘুষ গ্রহণ বা প্রদান:</li>
				<li><span style="font-weight:bold">ঘ।</span> বিনা ছুটিতে অভ্যাগত অনুপস্থিতি অথবা ছুটি না নিয়া এক সঙ্গে দশ দিনের অধিক সময় অনুপস্থিত।</li>
				<li><span style="font-weight:bold">ঙ।</span> অভ্যাগত বিলম্বে উপস্থিত।</li>
				<li><span style="font-weight:bold">চ।</span> প্রতিষ্ঠানের প্রযোজ্য কোন আইন, বিধি বা প্রবিধানের অভ্যাসগত লংঘন।</li>
				<li><span style="font-weight:bold">ছ।</span> প্রতিষ্ঠানে উচ্ছৃঙ্খল বা দাংগা হাঙ্গামামূলক আচরন, অথবা শৃঙ্খলা হানিকর কোন কর্ম।</li>
				<li><span style="font-weight:bold">জ।</span> কাজ কর্মে অভ্যাসগত গাফিলতি;</li>
				<li><span style="font-weight:bold">ঝা।</span> প্রধান পরিদর্শক কর্তৃক অনুমোদিত চাকুরী সংক্রান্ত, শৃঙ্খলা বা আচরনসহ যে কোব বিধির অভ্যাসগত লংঘন।</li>
				<li><span style="font-weight:bold">ঞ।</span> মালিকের অফিসিয়াল রেকর্ডের রদবদল, জালকরন, অন্যায় পরিবর্তন, উহার ক্ষতিকরন বা উহা হারাইয়া ফেলা;</li>
			</ul>
		</li>
		<li><b>ঠিকানা প্রদানঃ</b> আপনি/ আপনার ঠিকানা পরিবর্তন করিলে সাথে সাথে কর্তৃপক্ষ কে লিখিতভাবে জানাইতে হইবে।</li>
		<li><b>আইডি কার্ডঃ</b> প্রত্যেক শ্রমিক কর্মচারীকে আইডি কার্ড প্রদান করা হয় এবং উক্ত কার্ডটি কারখানা প্রবেশ ও বাহিরের সময় প্রধান ফটকে নিরাপত্তা বিভাগে প্রদর্শন করতে হবে। এবং কর্মকালীন সময়ে গলায় অথবা শরীরের দৃশ্যমান হয় এমন স্থানে ঝুলন্ত অবস্থায় রাখতে হবে।</li>
		<li><b>হাজিরা কার্ডঃ</b> প্রত্যেক শ্রমিক/কর্মচারী কে তাহাদের দৈনন্দিন হাজিরা নিশ্চিত করিবার জন্য হাজিরা কার্ড প্রদান করা হয়। উক্ত কার্ডটি কারখানায় উপস্থিত হয়ে প্রবেশ করার সময় সিকিউরিটি গেটে জমা দিতে হবে এবং ছুটির সময় কার্ডটিতে আপনার হাজিরা নিশ্চিত করে নিজ কর্মস্থলে ত্যাগ করতে হবে।</li>
		<li><b>মজুরী বৃদ্ধি/পদোন্নতিঃ</b> প্রতি বছর আপনার কর্মদক্ষদা মূল্যায়ন করে পদোন্নতি প্রদান এবং আপনার বেতন বৃদ্ধি করা হবে। তবে কমপক্ষে ৯% হার অবশ্যই বৃদ্ধি করা হবে।</li>
		<li><b>সার্ভিস বইঃ</b> বাংলাদেশ শ্রম আইন ২০০৬ অনুযায়ী প্রত্যেক শ্রমিকের জন্য কোম্পনীর খরচে সার্ভিস বই চালুর ব্যবস্থা আছে।</li>
	</ol>
</div>

<div style="margin-top:30px; font-size:15px;">
	আমি {{ $employee->name ?? '................' }} অত্র নিয়োগপত্র পাঠ করেছি/ আমাকে পাঠ করে শোনানো হয়েছে। এতে বর্ণিত শর্তাদি আমি সম্পূর্ণক্রমে অবগত হয়ে, কারো দ্বারা প্ররোচিত না হয়ে কারো কোনরূপ জোর জবরদস্তি ছাড়াই স্বেচ্ছায় ও স্বজ্ঞানে উপরোক্ত শর্ত মেনে নিয়ে, এই নিয়োগপত্রে স্বাক্ষর করে নিয়োগপত্র এবং একটি শ্রমিক সহায়িকা গ্রহন করছি এবং কাজে যোগদান করছি।
</div>

<table style="width:100%; margin-top:30px; font-size:15px;">
	<tr>
		<td>তারিখ: {{ now()->format('d/m/Y') }}</td>
		<td style="text-align:center">কর্মচারীর স্বাক্ষর</td>
		<td style="text-align:right">প্রশাসন, মানব সম্পদ বিভাগ</td>
	</tr>
</table>
