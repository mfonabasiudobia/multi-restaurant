<template>
    <div class="w-full">
        <!-- Main Display -->
        <div class="rounded-lg overflow-hidden">
            <swiper
                :modules="modules"
                :navigation="true"
                class="main-swiper"
                @swiper="setMainSwiper"
                :initialSlide="getInitialSlideIndex()"
            >
                <!-- Video Slides -->
                <swiper-slide
                    v-for="(video, index) in videos"
                    :key="`video-${video.id || index}`"
                    class="media-slide flex justify-center items-center"
                >
                    <div class="relative w-full flex items-center justify-center overflow-hidden">
                        <video
                            ref="videoRefs"
                            :src="video.src"
                            :poster="video.thumbnail"
                            class="w-full h-auto max-h-[80vh] object-contain rounded-lg video-element"
                            playsinline
                            preload="metadata"
                            :autoplay="!isMobile"
                            muted
                            @loadeddata="handleVideoLoaded(index)"
                            @play="handlePlay(index)"
                            @pause="handlePause(index)"
                            @ended="handleEnded(index)"
                        >
                            <source :src="video.src" type="video/mp4">
                            <track kind="captions" src="" label="English" />
                        </video>
                        <!-- Custom Video Controls -->
                        <div 
                            class="absolute inset-0 flex items-center justify-center cursor-pointer"
                            @click.stop="togglePlay(video, index)"
                        >
                            <div 
                                v-if="shouldShowPlayButton(index)"
                                class="w-16 h-16 md:w-20 md:h-20 bg-black/40 rounded-full flex items-center justify-center transition-opacity hover:bg-black/60"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 md:h-10 md:w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                </svg>
                            </div>
                        </div>
                        
                        <!-- Video Controls Bar (always visible) -->
                        <div 
                            class="absolute bottom-0 left-0 right-0 bg-black/40 backdrop-blur-sm p-2 flex items-center gap-2 w-full max-w-full overflow-hidden rounded-b-lg"
                            @click.stop
                        >
                            <!-- Play/Pause Button -->
                            <button @click.stop="togglePlay(video, index)" class="text-white p-1 rounded-full hover:bg-white/10 transition-colors flex-shrink-0">
                                <svg v-if="isVideoPlaying[index]" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                </svg>
                            </button>
                            
                            <!-- Rewind 10s -->
                            <button @click.stop="seekVideo(index, -10)" class="text-white p-1 rounded-full hover:bg-white/10 transition-colors flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                </svg>
                            </button>
                            
                            <!-- Progress Bar -->
                            <div class="relative flex-grow h-1.5 bg-white/30 rounded-full overflow-hidden cursor-pointer min-w-0" @click="handleProgressBarClick($event, index)">
                                <div class="absolute top-0 left-0 h-full bg-white rounded-full" :style="{ width: getVideoProgress(index) + '%' }"></div>
                            </div>
                            
                            <!-- Forward 10s -->
                            <button @click.stop="seekVideo(index, 10)" class="text-white p-1 rounded-full hover:bg-white/10 transition-colors flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.933 12.8a1 1 0 000-1.6L6.6 7.2A1 1 0 005 8v8a1 1 0 001.6.8l5.333-4zM19.933 12.8a1 1 0 000-1.6l-5.333-4A1 1 0 0013 8v8a1 1 0 001.6.8l5.333-4z" />
                                </svg>
                            </button>
                            
                            <!-- Time Display -->
                            <div class="text-white text-xs font-medium hidden sm:block flex-shrink-0">
                                {{ formatTime(videoCurrentTimes[index] || 0) }} / {{ formatTime(videoDurations[index] || 0) }}
                            </div>
                        </div>
                    </div>
                </swiper-slide>

                <!-- Photo Slides -->
                <swiper-slide
                    v-for="(photo, index) in photos"
                    :key="`photo-${photo.id || index}`"
                    class="media-slide flex justify-center items-center"
                >
                    <div class="relative w-full flex justify-center">
                        <ZoomableImage 
                            :src="photo.thumbnail" 
                            :alt="`Product photo ${index + 1}`"
                            class="w-full h-auto max-h-[80vh] object-contain rounded-lg"
                        />
                    </div>
                </swiper-slide>
            </swiper>
        </div>
    </div>
</template>

<style scoped>
.main-swiper {
    width: 100%;
}

.media-slide {
    padding: 0;
    height: auto;
    min-height: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Video styles */
video {
    background: transparent !important;
    border: none !important;
    opacity: 1 !important;
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

/* Navigation button styles */
:deep(.swiper-button-next),
:deep(.swiper-button-prev) {
    background-color: rgba(255, 255, 255, 0.9);
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    color: #1e293b;
}

:deep(.swiper-button-next)::after,
:deep(.swiper-button-prev)::after {
    font-size: 0.875rem;
}

/* Mobile styles */
@media (max-width: 768px) {
    :deep(.swiper-button-next),
    :deep(.swiper-button-prev) {
        display: none;
    }
    
    video, .zoomable-image {
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
    }
}

/* Video styles */
video::-webkit-media-controls {
    display: none !important;
}

video::-webkit-media-controls-panel {
    display: none !important;
}

video::-webkit-media-controls-play-button {
    display: none !important;
}

video::-webkit-media-controls-timeline {
    display: none !important;
}

video::-webkit-media-controls-current-time-display {
    display: none !important;
}

video::-webkit-media-controls-time-remaining-display {
    display: none !important;
}

video::-webkit-media-controls-mute-button {
    display: none !important;
}

video::-webkit-media-controls-volume-slider {
    display: none !important;
}

video::-webkit-media-controls-fullscreen-button {
    display: none !important;
}

/* Add pointer-events to video to allow touch interaction */
video {
    pointer-events: auto;
    cursor: pointer;
}

/* Video loading and hover effects */
video.video-element {
    transition: opacity 0.3s ease;
}

video.video-element:not([loaded]) {
    opacity: 0.7;
}

video.video-element:hover {
    filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}

/* Container styles */
.video-container {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}
</style>
<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation } from 'swiper/modules';
import ZoomableImage from './ZoomableImage.vue';

// Import required styles
import 'swiper/css';
import 'swiper/css/navigation';

const props = defineProps({
    videos: {
        type: Array,
        default: () => []
    },
    photos: {
        type: Array,
        default: () => []
    },
    initialMediaType: {
        type: String,
        default: 'image'
    },
    initialMediaIndex: {
        type: Number,
        default: 0
    }
});

// Define modules array for Swiper
const modules = [Navigation];

const mainSwiper = ref(null);
const videoRefs = ref([]);
const isVideoPlaying = ref({});
const hasStartedPlaying = ref({});
const loadedVideos = ref({});
const isMobile = ref(false);
const autoplaySupported = ref(true);

// Video control state variables
const videoCurrentTimes = ref({});
const videoDurations = ref({});
const videoProgressInterval = ref(null);

const setMainSwiper = (swiper) => {
    mainSwiper.value = swiper;
};

const getInitialSlideIndex = () => {
    if (props.initialMediaType === 'video') {
        return props.initialMediaIndex;
    } else {
        return props.videos.length + props.initialMediaIndex;
    }
};

// Watch for changes in initial media selection
watch([() => props.initialMediaType, () => props.initialMediaIndex], () => {
    if (mainSwiper.value) {
        mainSwiper.value.slideTo(getInitialSlideIndex());
    }
});

// Helper function to determine if play button should be shown
const shouldShowPlayButton = (index) => {
    return !isVideoPlaying.value[index] && (hasStartedPlaying.value[index] || isMobile.value);
};

// Event handlers
const handleVideoLoaded = (index) => {
    loadedVideos.value[index] = true;
    
    // Store video duration when loaded
    if (videoRefs.value[index]) {
        videoDurations.value[index] = videoRefs.value[index].duration;
    }
};

const handlePlay = (index) => {
    isVideoPlaying.value[index] = true;
    hasStartedPlaying.value[index] = true;
};

const handlePause = (index) => {
    isVideoPlaying.value[index] = false;
};

const handleEnded = (index) => {
    isVideoPlaying.value[index] = false;
    // Loop the video on desktop
    if (!isMobile.value) {
        const video = videoRefs.value[index];
        if (video) {
            video.play().then(() => {
                isVideoPlaying.value[index] = true;
            });
        }
    }
};

const togglePlay = async (video, index) => {
    const videoElement = videoRefs.value[index];
    
    if (!loadedVideos.value[index]) {
        // If video isn't loaded yet, load it first
        try {
            await videoElement.load();
            await new Promise(resolve => {
                videoElement.addEventListener('loadeddata', resolve, { once: true });
            });
            loadedVideos.value[index] = true;
        } catch (error) {
            console.error('Error loading video:', error);
            return;
        }
    }
    
    if (videoElement.paused) {
        pauseAllVideos(); // Pause all other videos first
        try {
            await videoElement.play();
            isVideoPlaying.value[index] = true;
        } catch (error) {
            console.error('Error playing video:', error);
        }
    } else {
        videoElement.pause();
        isVideoPlaying.value[index] = false;
    }
    hasStartedPlaying.value[index] = true;
};

onMounted(() => {
    // Check if device is mobile
    isMobile.value = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    // Set up interval to update video progress
    videoProgressInterval.value = setInterval(() => {
        updateVideoProgress();
    }, 250);

    if (mainSwiper.value) {
        mainSwiper.value.on('slideChange', () => {
            pauseAllVideos();
            if (!isMobile.value) {
                // Try to autoplay the current video after slide change
                const currentIndex = mainSwiper.value.activeIndex;
                if (currentIndex < videoRefs.value.length) {
                    const currentVideo = videoRefs.value[currentIndex];
                    if (currentVideo) {
                        currentVideo.play().then(() => {
                            isVideoPlaying.value[currentIndex] = true;
                        }).catch(() => {
                            isVideoPlaying.value[currentIndex] = false;
                        });
                    }
                }
            }
        });
    }

    // Initialize video states
    if (props.videos && props.videos.length > 0) {
        props.videos.forEach((_, index) => {
            // Set initial loading state
            loadedVideos.value[index] = false;
            videoCurrentTimes.value[index] = 0;
            videoDurations.value[index] = 0;
        });
    }
    
    // Set up video references after component is mounted
    nextTick(() => {
        if (videoRefs.value && videoRefs.value.length > 0) {
            videoRefs.value.forEach((video, index) => {
                if (video) {
                    // Add loaded data event listener if not already set by v-on
                    video.addEventListener('loadeddata', () => {
                        loadedVideos.value[index] = true;
                        videoDurations.value[index] = video.duration;
                    });
                    
                    // Set initial playing state for desktop autoplay
                    if (!isMobile.value) {
                        video.play().then(() => {
                            isVideoPlaying.value[index] = true;
                            hasStartedPlaying.value[index] = false;
                        }).catch(() => {
                            isVideoPlaying.value[index] = false;
                            hasStartedPlaying.value[index] = true;
                        });
                    }
                }
            });
        }
    });
});

const pauseAllVideos = () => {
    if (videoRefs.value && videoRefs.value.length > 0) {
        videoRefs.value.forEach((video, index) => {
            if (video) {
                video.pause();
                isVideoPlaying.value[index] = false;
            }
        });
    }
};

// Format time for display (converts seconds to MM:SS format)
const formatTime = (seconds) => {
    if (!seconds || isNaN(seconds)) return '00:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

// Get video progress percentage
const getVideoProgress = (index) => {
    const currentTime = videoCurrentTimes.value[index] || 0;
    const duration = videoDurations.value[index] || 0;
    if (duration === 0) return 0;
    return (currentTime / duration) * 100;
};

// Update video progress for all playing videos
const updateVideoProgress = () => {
    videoRefs.value.forEach((video, index) => {
        if (video && !video.paused) {
            videoCurrentTimes.value[index] = video.currentTime;
        }
    });
};

// Handle progress bar click to seek
const handleProgressBarClick = (event, index) => {
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const clickPosition = (event.clientX - rect.left) / rect.width;
    
    if (videoRefs.value[index] && videoDurations.value[index]) {
        const newTime = clickPosition * videoDurations.value[index];
        videoRefs.value[index].currentTime = newTime;
        videoCurrentTimes.value[index] = newTime;
    }
};

// Seek video forward or backward
const seekVideo = (index, seconds) => {
    if (videoRefs.value[index]) {
        const video = videoRefs.value[index];
        const newTime = Math.max(0, Math.min(video.duration, video.currentTime + seconds));
        video.currentTime = newTime;
        videoCurrentTimes.value[index] = newTime;
    }
};

const emit = defineEmits(['zoomChange']);

const handleZoomChange = (isZooming) => {
    emit('zoomChange', isZooming);
};
</script>