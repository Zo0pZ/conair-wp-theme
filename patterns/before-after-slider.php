<?php
/**
 * Title: Before & After Slider — Real Results
 * Slug: conair-theme/before-after-slider
 * Categories: conair-sections, conair-interactive
 * Description: Interactive before/after image comparison slider with WCAG keyboard support.
 *              JavaScript controlled by assets/js/conair-theme.js — requires IDs ba-slider, ba-after-layer, ba-divider.
 */
?>
<!-- wp:html -->
<section id="results" class="py-16 sm:py-24" style="background:#0c0c0c;">
  <div class="max-w-5xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-xl mx-auto mb-10 sm:mb-14 reveal">
      <div class="badge mb-4 mx-auto" style="display:inline-flex;">Real Results</div>
      <h2 class="font-black text-white leading-tight mb-3" style="font-size:clamp(1.8rem,3vw,2.4rem);">See the ConAir Difference</h2>
      <p style="font-size:15px;color:#9a9a9a;">Drag the handle — or focus it and use arrow keys — to reveal the transformation.</p>
    </div>
    <div
      class="ba-container w-full rounded-2xl reveal"
      id="ba-slider"
      tabindex="0"
      role="slider"
      aria-label="Before and after comparison slider"
      aria-valuemin="3"
      aria-valuemax="97"
      aria-valuenow="50"
      style="height:340px;max-height:52vh;border:1px solid #2e2e2e;box-shadow:0 0 60px rgba(0,0,0,0.8);"
    >
      <div class="before-img absolute inset-0" aria-hidden="true"></div>
      <div class="ba-label ba-label-before" aria-hidden="true">Before</div>
      <div class="ba-after" id="ba-after-layer" style="width:50%;" aria-hidden="true">
        <div class="after-img" style="position:absolute;inset:0;width:100vw;max-width:none;"></div>
        <div class="ba-label ba-label-after">After</div>
      </div>
      <div class="ba-divider" id="ba-divider" style="left:50%;" aria-hidden="true">
        <div class="ba-handle"></div>
      </div>
    </div>
    <p class="text-center text-sm mt-4 font-medium reveal" style="color:#7a7a7a;">← Arrow keys or drag to compare →</p>
  </div>
</section>
<!-- /wp:html -->
