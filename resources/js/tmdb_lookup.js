document.addEventListener('alpine:init', () => {
  Alpine.data('tmdbLookup', (tmdbId = '', lookupUrl = '') => ({
    tmdbId,
    lookupUrl,
    loading:  false,
    preview:  null,
    error:    null,
    imported: false,

    get meta() {
      if (! this.preview) return '';
      return [
        this.preview.release_date?.slice(0, 4),
        this.preview.duration ? this.preview.duration + ' min' : null,
        this.preview.genre,
        this.preview.rating,
        this.preview.tmdb_rating ? '⭐ ' + this.preview.tmdb_rating + '/10' : null,
      ].filter(Boolean).join(' · ');
    },

    async lookup() {
      if (! this.tmdbId) {
        this.error   = 'Please enter a TMDB ID first.';
        this.preview = null;
        return;
      }

      this.loading  = true;
      this.preview  = null;
      this.error    = null;
      this.imported = false;

      try {
        const res  = await fetch(`${this.lookupUrl}?tmdb_id=${this.tmdbId}`);
        const data = await res.json();

        if (! res.ok) {
            this.error = data.error ?? 'Something went wrong.';
            return;
        }

        this.preview = data;
      } catch (e) {
        this.error = 'Network error. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    importData() {
      if (! this.preview) return;

      this.setField('title',        this.preview.title);
      this.setField('description',  this.preview.description);
      this.setField('director',     this.preview.director);
      this.setField('actors',       this.preview.actors);
      this.setField('trailer_url',  this.preview.trailer_url);
      this.setField('release_date', this.preview.release_date);
      this.setField('duration',     this.preview.duration);
      this.setSelect('genre',       this.preview.genre);
      this.setSelect('rating',      this.preview.rating);

      this.imported = true;
      setTimeout(() => this.imported = false, 2000);
    },

    setField(name, value) {
      const el = document.querySelector(`[name="${name}"]`);
      if (el && value != null) el.value = value;
    },

    setSelect(name, value) {
      const el = document.querySelector(`select[name="${name}"]`);
      if (! el || value == null) return;
      [...el.options].forEach(opt => {
        opt.selected = opt.value.toLowerCase() === String(value).toLowerCase();
      });
    },
  }));
});