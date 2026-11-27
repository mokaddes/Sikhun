<?php

namespace Tests\Feature;

use App\Models\QuestionBank;
use App\Services\Questions\QuestionJsonImportService;
use App\Services\Text\BijoyToUnicodeConverter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HscJsonImportVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_bijoy_to_unicode_converter_decodes_bangla_words_and_symbols(): void
    {
        $converter = new BijoyToUnicodeConverter();

        $this->assertSame('ঢাকা বোর্ড-২০১৭', $converter->convert('XvKv †evW©-2017'));
        $this->assertSame('হিসাববিজ্ঞান: প্রথম পত্র', $converter->convert('wnmveweÁvb: cÖ_g cÎ'));
        $this->assertSame('কুমিল্লা বোর্ড', $converter->convert('Kzwgjøv †evW©'));
        $this->assertSame('বহুনির্বাচনি প্রশ্নোত্তর', $converter->convert('eûwbe©vPwb cÖ‡kœvËi'));
    }

    public function test_generated_hsc_json_imports_cleanly_into_question_bank(): void
    {
        $samplePath = base_path('qsn-json/hsc_physics_mcq.json');
        $this->assertFileExists($samplePath);

        /** @var QuestionJsonImportService $importer */
        $importer = app(QuestionJsonImportService::class);
        $result = $importer->importFile($samplePath, ['segment' => 'hsc']);

        $this->assertGreaterThan(0, $result['total_inserted']);
        $this->assertSame(0, QuestionBank::where('segment', '!=', 'hsc')->count());

        $first = QuestionBank::first();
        $this->assertNotNull($first);
        $this->assertSame('hsc', $first->segment);
        $this->assertSame('mcq', $first->question_type);
        $this->assertContains($first->correct_option, ['A', 'B', 'C', 'D']);
        $this->assertCount(4, is_array($first->options) ? $first->options : json_decode((string) $first->options, true));
    }
}
