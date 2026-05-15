# Member Share Management Guide

কীভাবে একজন member-এর share বাড়ানো বা কমানো হবে — step-by-step গাইড।

---

## ভূমিকা

প্রতিটি member-এর একটি `number_of_share` থাকে। প্রতি মাসে তাদের due হিসাব হয়:

```
Monthly Due = number_of_share × share_price (configuration থেকে)
```

জীবনের কোনো একটা সময়ে member share বাড়াতে বা কমাতে পারে। এই system "Forward-only" model অনুসরণ করে —

- ✅ পরিবর্তনের আগের মাসগুলোর due **অপরিবর্তিত** থাকবে
- ✅ পরিবর্তনের পরের মাসগুলোতে **নতুন share count** অনুযায়ী due তৈরি হবে
- ❌ Past dues retroactively change হয় না

---

## Scenario 1 — Share বাড়ানো (Increase)

**উদাহরণ:** Md Abu Ehsan এর 2 share ছিল ৬ মাস। ১ম জুন থেকে সে 4 share করতে চাইছে।

### Step-by-step

1. **Admin Login** → বাম পাশের sidebar থেকে **Members** এ যাও
2. Member list থেকে Md Abu Ehsan এর সামনে **Edit (✏️)** আইকনে ক্লিক করো
3. Form-এ `Number of Share` field-এ **2** এর জায়গায় **4** লিখো
4. **Update** বাটনে ক্লিক করো

### এর ফলে যা হবে

| বিষয় | পরিবর্তন |
|------|---------|
| Past 6 মাসের dues | অপরিবর্তিত — `2 × 5000 = 10,000` প্রতি মাস |
| Past payments | অপরিবর্তিত |
| `users.number_of_share` | 2 → 4 |
| Future due generation | নতুন value (4) ব্যবহার করবে |

### Next Month-এ যা করতে হবে

পরের মাসের শুরুতে (যেমন ১ম জুন এ):

5. **Admin → Dues → "Generate Due"** বাটনে ক্লিক করো
6. June 2026 এর জন্য নতুন due `4 × 5000 = 20,000` তৈরি হবে
7. Member কে এই amount পরিশোধ করতে হবে

---

## Scenario 2 — Share কমানো (Decrease)

**উদাহরণ:** Md Abu Ehsan এর 3 share ছিল ৬ মাস। সে আর্থিক কারণে 1 share কমিয়ে 2 share করতে চাইছে।

### Step-by-step

#### Part A: Share Count আপডেট

1. **Admin → Members** → Md Abu Ehsan এর **Edit** এ যাও
2. `Number of Share` field-এ **3** এর জায়গায় **2** লিখো
3. **Update** ক্লিক করো

#### Part B: Refund Withdrawal Entry

কমানো 1 share এর accumulated value member কে ফেরত দিতে হবে।

**হিসাব:**
```
Refund Amount = কমানো_share × পরিশোধিত_মাস × share_price
              = 1 × 6 × 5000
              = ৳ 30,000
```

4. **Admin → Withdrawals → Add New** এ যাও
5. ফর্ম পূরণ করো:
   - **Member:** Md Abu Ehsan
   - **Amount:** 30000
   - **Withdrawal Date:** আজকের তারিখ
   - **Payment Method:** Cash / Bank / Mobile Banking
   - **Note:** "১ share কমানোর জন্য refund (Jan–Jun 2026)"
6. **Save** ক্লিক করো

### এর ফলে যা হবে

| বিষয় | পরিবর্তন |
|------|---------|
| Past 6 মাসের dues | অপরিবর্তিত — `3 × 5000 = 15,000` প্রতি মাস |
| Past payments | অপরিবর্তিত |
| `users.number_of_share` | 3 → 2 |
| একটি Withdrawal entry | 30,000 (refund) তৈরি হলো |
| Org Net Fund | 30,000 কমলো (cash out) |
| Future due generation | নতুন value (2) ব্যবহার করবে |

### Next Month-এ

7. **Admin → Dues → "Generate Due"** ক্লিক করো
8. পরের মাস থেকে due `2 × 5000 = 10,000` তৈরি হবে

---

## গুরুত্বপূর্ণ বিষয়

### ১. Past dues কেন পরিবর্তন হয় না?

প্রতিটি due record তৈরির সময় সেই মুহূর্তের share count snapshot করে রাখে। তাই historical accuracy বজায় থাকে — যা ছিল তা ছিল।

### ২. কোন কোন মাসের জন্য Refund দিতে হবে?

শুধু সেই মাসগুলোর জন্য যেগুলোতে member **পরিশোধ করেছে** (paid)। যদি কোনো মাস unpaid থাকে, ঐ মাসের জন্য refund হিসাবের মধ্যে আসবে না — কারণ সে টাকা পরিশোধই করেনি।

**সূত্র:**
```
Refund = কমানো_share × পরিশোধিত_মাস_সংখ্যা × share_price
```

### ৩. Generate Due বাটন কখন ক্লিক করতে হয়?

- প্রতি মাসের শুরুতে (যেমন ১ম তারিখে) একবার ক্লিক করলেই হয়
- সব active member-এর জন্য চলতি মাসের due তৈরি হয়
- ইতিমধ্যে তৈরি হওয়া dues skip হয় (duplicate তৈরি হয় না)
- নতুন member বা share পরিবর্তিত member-এর জন্য current `number_of_share` snapshot হয়

### ৪. Frontend public site কী দেখাবে?

Frontend (`/`, `/member/{id}`) সরাসরি `dues` table থেকে hisab টানে:
- যেসব dues `unpaid` বা `partial` তাদের `remaining_amount` যোগ করে
- তাই share পরিবর্তনের পরের scenario-ও সঠিকভাবে reflect হয়

---

## Quick Reference Table

| Action | Field পরিবর্তন | Extra Entry লাগবে? | Refund? |
|--------|----------------|---------------------|---------|
| Share বাড়ানো (3→5) | `users.number_of_share` = 5 | না | না |
| Share কমানো (3→2) | `users.number_of_share` = 2 | হ্যাঁ — Withdrawal | হ্যাঁ |
| পরে আবার বাড়ানো (2→4) | `users.number_of_share` = 4 | না | না |

---

## Scenario 3 — নতুন Member পরে যোগ দিল (Lump-sum payment)

**উদাহরণ:** System start হয়েছিল May 2026। November 2026 এ একজন নতুন member যোগ দিল 1 share নিয়ে। তার back-due `7 × 5000 = 35,000`।

### Step-by-step

1. **Admin → Members → Add New** এ যাও
2. Member তথ্য পূরণ করো:
   - **Name:** নতুন সদস্যের নাম
   - **Number of Share:** 1
   - **Join Date:** 2026-11-01 (যেদিন যোগ দিল)
3. **Save** করো
4. **Admin → Dues → Generate Due** click করো → May থেকে November পর্যন্ত 7 মাসের dues তৈরি হবে (প্রতিটা 5000)
5. **Admin → Deposits → Add New** এ যাও
6. ফর্ম পূরণ করো:
   - **Member:** নতুন member
   - **Deposit Type:** Share Deposit
   - **Amount:** 35000 (এক সাথে সব শোধ)
   - **For Month:** যেকোনো একটা মাস (system auto-distribute করবে)
   - **Payment Method:** Cash/Bank/Mobile
7. **Save** ক্লিক করো

### এর ফলে যা হবে

System automatic ভাবে ৳35,000 deposit-কে chronologically distribute করবে:

| Month | Due | Paid | Status |
|-------|-----|------|--------|
| May 2026 | 5,000 | 5,000 | ✅ paid |
| Jun 2026 | 5,000 | 5,000 | ✅ paid |
| Jul 2026 | 5,000 | 5,000 | ✅ paid |
| Aug 2026 | 5,000 | 5,000 | ✅ paid |
| Sep 2026 | 5,000 | 5,000 | ✅ paid |
| Oct 2026 | 5,000 | 5,000 | ✅ paid |
| Nov 2026 | 5,000 | 5,000 | ✅ paid |

### Advance Payment Scenario (Overpayment)

যদি member শুধু চলতি মাসের চেয়ে বেশি payment করে — অর্থাৎ future মাসের জন্য advance — system **automatically future dues create করে দেবে**।

**উদাহরণ:** Member-এর 1 share, system এ এখন শুধু May 2026 এর due আছে (৳5,000)। সে May তে ৳10,000 পেইড করল।

| Month | Due | Paid | Status | কিভাবে তৈরি |
|-------|-----|------|--------|-------------|
| May 2026 | 5,000 | 5,000 | ✅ paid | Generate Due বাটনে |
| Jun 2026 | 5,000 | 5,000 | ✅ paid | **Auto-created from overpayment** |

System member-এর current `number_of_share × share_price` ব্যবহার করে যত মাস লাগে তত মাস future due তৈরি করে, যতক্ষণ না overpayment-এর সব টাকা use হয়ে যায়।

### Partial Payment Scenario

যদি member শুধু ৳22,000 দেয় (পুরো শোধ করতে পারল না):

| Month | Due | Paid | Status |
|-------|-----|------|--------|
| May 2026 | 5,000 | 5,000 | ✅ paid |
| Jun 2026 | 5,000 | 5,000 | ✅ paid |
| Jul 2026 | 5,000 | 5,000 | ✅ paid |
| Aug 2026 | 5,000 | 5,000 | ✅ paid |
| Sep 2026 | 5,000 | **2,000** | ⚠️ partial |
| Oct 2026 | 5,000 | 0 | ❌ unpaid |
| Nov 2026 | 5,000 | 0 | ❌ unpaid |

System সব সময় **পুরাতন মাস আগে** clear করে। পরবর্তীতে যত deposit করবে, সেগুলো একইভাবে আগের unpaid মাসগুলোতে যাবে।

---

## Scenario 4 — Share Adjustment (Retroactive)

`Member Finance → Share Adjustment` menu থেকে এই feature use করো — যখন past months সহ retroactively share count পরিবর্তন করতে হবে।

### কখন এটা use করবে

| Need | Tool |
|------|------|
| শুধু future থেকে পরিবর্তন | Members → Edit → `number_of_share` (Scenario 1/2) |
| Past সহ retroactively change | **Share Adjustment** (এই scenario) |

### Increase (3 → 4) দিয়ে কী হয়

1. Form-এ Member, Type=Increase, Count=1 দিলে → live preview দেখাবে
2. Calculation:
   - Months elapsed: 6
   - Expected at old rate: 3 × 5000 × 6 = 90,000
   - Expected at new rate: 4 × 5000 × 6 = 120,000
   - **Member owes: ৳30,000**
3. Apply করলে:
   - Past 6 মাসের দু-এর সব `due_amount` 15,000 → 20,000 update
   - ৳30,000-এর share_deposit deposit auto-create
   - User-এর `number_of_share = 4`
   - Audit row save
   - Reconcile run → past dues fully paid দেখাবে

### Decrease (3 → 2) দিয়ে কী হয়

1. Form-এ Type=Decrease, Count=1, Refund destination=Withdrawal বা Extra Savings
2. Calculation:
   - Expected at new rate: 2 × 5000 × 6 = 60,000
   - Expected at old rate: 3 × 5000 × 6 = 90,000
   - **Refund to member: ৳30,000**
3. Apply করলে:
   - Past 6 মাসের সব `due_amount` 15,000 → 10,000 update
   - **Refund destination অনুযায়ী:**
     - Withdrawal হলে → ৳30,000 এর withdrawal entry
     - Extra Savings হলে → ৳30,000 এর extra_savings deposit
   - User-এর `number_of_share = 2`
   - Audit row save
   - Reconcile run → past dues fully paid (new rate-এ)

### Audit Trail

প্রতিটা adjustment `share_adjustments` table-এ save হয়। **Member Finance → Share Adjustment → History** থেকে দেখা যাবে:
- কে, কবে, কী adjustment করল
- Linked deposit/withdrawal voucher
- Note + effective_date

---

## Edge Cases

### Member-এর past dues unpaid থাকলে share কমাতে চাইলে?

আগে past unpaid dues clear করতে হবে। তারপর share কমানোর আগে admin সিদ্ধান্ত নেবে:
- **Option 1:** Member বাকি unpaid দিয়ে clear করবে, তারপর full refund পাবে
- **Option 2:** Refund amount থেকে unpaid বাদ দিয়ে net amount refund হবে
- **Option 3:** Past unpaid dues-গুলো soft-delete বা adjust করো (admin discretion)

### Share কমানো / বাড়ানোর জন্য কি কোনো history record থাকে?

বর্তমানে আলাদা share history table নেই। কিন্তু:
- Past `dues` table-এ snapshot আছে কোন মাসে কত due ছিল
- Withdrawal entry থেকে refund history বোঝা যাবে
- প্রয়োজন হলে ভবিষ্যতে `share_histories` table যোগ করা যাবে

### Share = 0 করা যাবে?

প্রযুক্তিগতভাবে যাবে, কিন্তু এটা মানে member organization থেকে বিচ্ছিন্ন হচ্ছে। সেক্ষেত্রে:
1. সব past shares এর accumulated value withdraw করো
2. Member-এর status `inactive` করো (delete করার বদলে)
3. Future Generate Due এ এই member skip হবে

---

## Configuration Settings

এই calculations-এর জন্য নিচের settings লাগে:

| Setting | Value | কোথায় |
|---------|-------|--------|
| Share Price | ৳ 5,000 | Admin → System Configuration |
| Start Date | 2026-05-01 | Admin → System Configuration |

`Generate Due` বাটন এই দুটো value ব্যবহার করেই dues তৈরি করে। পরিবর্তন করলে শুধু পরবর্তী generation এ effect হবে।

---

## কোন বিষয়ে সাহায্য?

- সব scenarios automated — admin-কে শুধু **3টি ক্লিক** করতে হয় (Edit member, Save, Generate Due)
- Share কমানোর সময় শুধু একটি extra **Withdrawal entry** লাগে
- Frontend automatic update হয়, কোনো manual sync লাগে না
