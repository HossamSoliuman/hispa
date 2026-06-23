<x-report-layout :title="'الإقفال الشهري'" :document-number="''" :settings="$settings" :qr-code="$settings['qr_code'] ?? ''">
    <x-report-masthead :title="'الإقفال الشهري'" :subtitle="'إقفال شهر الصيد وتوزيع أرباح البحارة'" :settings="$settings" />

    {{-- Meta strip --}}
    <div class="meta-row">
        <span class="meta-item">
            <span class="lbl">الشهر:</span>
            <span class="val-box">يونيو</span>
        </span>
        <span class="meta-item">
            <span class="lbl">السنة:</span>
            <span class="val-box">2026</span>
        </span>
        <span class="meta-item">
            <span class="lbl">تاريخ الإقفال:</span>
            <span class="val-box">30/06/2026</span>
        </span>
    </div>

    {{-- 5 Summary cards --}}
    <table class="sum-cards">
        <tr>
            <td class="sum-head">1. إجمالي المبيعات</td>
            <td class="sum-head">2. إجمالي المصروفات</td>
            <td class="sum-head">3. الربح قبل التوزيع</td>
            <td class="sum-head">4. الاحتياطيات والاستقطاعات</td>
            <td class="sum-head">5. الربح القابل للتوزيع</td>
        </tr>
        <tr>
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">إجمالي المبيعات</td><td class="sum-v">126,044.00</td></tr>
                    <tr><td class="sum-k">مرتجعات المبيعات</td><td class="sum-v">0.00</td></tr>
                    <tr class="sum-total"><td class="sum-k">صافي المبيعات</td><td class="sum-v">126,044.00</td></tr>
                </table>
            </td>
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">مصاريف الرحلات</td><td class="sum-v">78,560.00</td></tr>
                    <tr><td class="sum-k">مصاريف تشغيلية وإدارية</td><td class="sum-v">6,350.00</td></tr>
                    <tr class="sum-total"><td class="sum-k">إجمالي المصروفات</td><td class="sum-v">84,910.00</td></tr>
                </table>
            </td>
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">صافي المبيعات</td><td class="sum-v">126,044.00</td></tr>
                    <tr><td class="sum-k">إجمالي المصروفات</td><td class="sum-v">84,910.00</td></tr>
                    <tr class="sum-total"><td class="sum-k">الربح قبل التوزيع</td><td class="sum-v">41,134.00</td></tr>
                </table>
            </td>
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">احتياطي صيانة القوارب</td><td class="sum-v">2,056.70</td></tr>
                    <tr><td class="sum-k">احتياطي طوارئ (3%)</td><td class="sum-v">1,234.02</td></tr>
                    <tr><td class="sum-k">استقطاع صندوق معدات</td><td class="sum-v">1,000.00</td></tr>
                    <tr class="sum-total"><td class="sum-k">إجمالي الاستقطاعات</td><td class="sum-v">4,290.72</td></tr>
                </table>
            </td>
            <td class="sum-card">
                <table class="sum-body">
                    <tr><td class="sum-k">الربح قبل التوزيع</td><td class="sum-v">41,134.00</td></tr>
                    <tr><td class="sum-k">إجمالي الاستقطاعات</td><td class="sum-v">4,290.72</td></tr>
                    <tr class="sum-total"><td class="sum-k">الربح القابل للتوزيع</td><td class="sum-v">36,843.28</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Distribution table --}}
    <div class="section-bar">6. توزيع أرباح البحارة</div>
    <table class="report-table" style="margin-bottom:14px;">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:18%">اسم البحار</th>
                <th>الوظيفة</th>
                <th>نسبة المشاركة</th>
                <th>الربح المستحق</th>
                <th>الدفعة المقدمة</th>
                <th>الصافي المستحق</th>
                <th style="width:10%">التوقيع</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td class="col-text">أحمد سالم</td>
                <td>نخاذ</td>
                <td>30%</td>
                <td class="col-num">11,052.98</td>
                <td class="col-num">2,000.00</td>
                <td class="col-num">9,052.98</td>
                <td style="color:#bbb;letter-spacing:2px;">................</td>
            </tr>
            <tr>
                <td>2</td>
                <td class="col-text">فهد علي</td>
                <td>مساعد نخاد</td>
                <td>25%</td>
                <td class="col-num">9,210.82</td>
                <td class="col-num">1,500.00</td>
                <td class="col-num">7,710.82</td>
                <td style="color:#bbb;letter-spacing:2px;">................</td>
            </tr>
            <tr>
                <td>3</td>
                <td class="col-text">محمد سعيد</td>
                <td>بحار</td>
                <td>20%</td>
                <td class="col-num">7,368.66</td>
                <td class="col-num">1,200.00</td>
                <td class="col-num">6,168.66</td>
                <td style="color:#bbb;letter-spacing:2px;">................</td>
            </tr>
            <tr>
                <td>4</td>
                <td class="col-text">سعيد الزهراني</td>
                <td>بحار</td>
                <td>15%</td>
                <td class="col-num">5,526.49</td>
                <td class="col-num">1,000.00</td>
                <td class="col-num">4,526.49</td>
                <td style="color:#bbb;letter-spacing:2px;">................</td>
            </tr>
            <tr>
                <td>5</td>
                <td class="col-text">خالد حسن</td>
                <td>بحار</td>
                <td>10%</td>
                <td class="col-num">3,684.33</td>
                <td class="col-num">800.00</td>
                <td class="col-num">2,884.33</td>
                <td style="color:#bbb;letter-spacing:2px;">................</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="col-text">الإجمالي</td>
                <td>100%</td>
                <td class="col-num">36,843.28</td>
                <td class="col-num">6,500.00</td>
                <td class="col-num">30,343.28</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- Closing summary --}}
    <table class="close-footer">
        <tr>
            <td class="cf-col" style="width:48%;">
                <div class="cf-card">
                    <div class="cf-head" style="background:#fff;color:#1a1a1a;border-bottom:1px solid #cfcfcf;">ملخص الإقفال</div>
                    <div class="cf-body">
                        <table class="cf-kv">
                            <tr><td class="cf-k">الربح القابل للتوزيع</td><td class="cf-v">36,843.28</td></tr>
                            <tr><td class="cf-k">إجمالي الدفعات المقدمة</td><td class="cf-v">6,500.00</td></tr>
                            <tr><td class="cf-k">إجمالي الصافي للبحارة</td><td class="cf-v">30,343.28</td></tr>
                            <tr><td class="cf-k">رصيد احتياطي الصيانة</td><td class="cf-v">2,056.70</td></tr>
                            <tr><td class="cf-k">رصيد احتياطي الطوارئ</td><td class="cf-v">1,234.02</td></tr>
                            <tr><td class="cf-k">رصيد صندوق المعدات</td><td class="cf-v">1,000.00</td></tr>
                        </table>
                    </div>
                </div>
            </td>
            <td class="cf-gap"></td>
            <td class="cf-col"></td>
        </tr>
    </table>

    {{-- Document footer --}}
    <div style="border-top: 1px solid #cfcfcf; margin-top: 18px; padding-top: 10px; text-align: center; font-size: 8pt; color: #888;">
        مؤسسة دار الحوت التجارية — جميع الحقوق محفوظة © 2026
    </div>
</x-report-layout>
