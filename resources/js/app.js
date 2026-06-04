import './bootstrap';
import './theme'

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('movieSearch', (oldMovieId = null, existingMovie = null) => ({
  query: existingMovie?.title ?? '',
  results: [],
  selectedMovie: existingMovie ?? null,
  open: false,
  loading: false,
  restoring: false,

  async init() {
    if (oldMovieId && !existingMovie) {
      this.restoring = true; 
      try {
        const res = await fetch(`/movies/search?id=${encodeURIComponent(oldMovieId)}`);
        const movie = await res.json();
        if (movie?.id) this.select(movie);
      } finally {
        this.restoring = false;
      }
    }
  },

  async search() {
    if (this.restoring) return;
    if (this.query.length < 1) {
      this.results = [];
      this.open = false;
      return;
    }
    this.loading = true;
    try {
      const res = await fetch(`/movies/search?q=${encodeURIComponent(this.query)}`);
      this.results = await res.json();
      this.open = true;
    } finally {
      this.loading = false;
    }
  },

  select(movie) {
    this.selectedMovie = movie;
    this.query = movie.title;
    this.open = false;
    // only blur if the ref is available (not during init restore)
    if (!this.restoring) this.$refs.input?.blur();
  },

  clear() {
    this.selectedMovie = null;
    this.query = '';
    this.results = [];
    this.$nextTick(() => this.$refs.input.focus());
  },

  focusResult(index) {
    const items = this.$el.querySelectorAll('li');
    items[index]?.focus();
  }
}));

Alpine.start();