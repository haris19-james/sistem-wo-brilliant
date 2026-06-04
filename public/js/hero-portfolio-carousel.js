function heroPortfolioCarousel(slideCount, intervalMs = 4000) {
    return {
        active: 0,
        slideCount,
        intervalMs,
        timer: null,
        reducedMotion: false,

        init() {
            this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (this.slideCount <= 1 || this.reducedMotion) {
                return;
            }
            this.start();
        },

        start() {
            this.pause();
            this.timer = setInterval(() => this.next(), this.intervalMs);
        },

        next() {
            this.active = (this.active + 1) % this.slideCount;
        },

        goTo(index) {
            if (index >= 0 && index < this.slideCount) {
                this.active = index;
            }
        },

        pause() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },

        resume() {
            if (!this.reducedMotion && this.slideCount > 1 && !this.timer) {
                this.start();
            }
        },
    };
}
