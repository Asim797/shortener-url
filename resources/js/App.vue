<template>
    <div class="container mt-5">
        <h1 class="text-center mb-4">URL Shortner</h1>
        <form @submit.prevent="submitUrl" class="mb-3">
            <div class="mb-3">
                <label for="url" class="form-label">Original URL:</label>
                <input v-model="originalUrl" type="url" id="url" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

        <div v-if="shortUrl" class="mb-3">
            <p class="mb-1">Short URL:</p>
            <a :href="shortUrl" target="_blank" class="btn btn-success">{{ shortUrl }}</a>
        </div>
        <div v-if="errorMessage" class="mb-3">
            <p class="mb-1 bg-danger text-white">Error: <span class="text-white">{{ errorMessage }}</span></p>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        data() {
            return {
                originalUrl: '',
                shortUrl: '',
                errorMessage: '',
            };
        },
        methods: {
            async submitUrl() {
                this.errorMessage = '';
                this.shortUrl = '';
                try {
                    const response = await axios.post('/shorten', { original_url: this.originalUrl });
                    this.shortUrl = response.data.shortened_url;
                } catch (error) {
                    this.errorMessage = error.response.data.error;
                }
            },
        },
    };
</script>