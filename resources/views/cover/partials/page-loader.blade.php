{{-- Page Loader Component --}}
<div id="page-loader" style="display: none;">
    <div class="loader-wrapper">
        <div class="loader-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <p class="loader-text mt-3">Loading...</p>
    </div>
</div>

<style>
    #page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(3px);
    }

    .loader-wrapper {
        text-align: center;
        background-color: #fff;
        padding: 2rem 3rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: fadeInScale 0.3s ease-out;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .loader-spinner .spinner-border {
        width: 3rem;
        height: 3rem;
        border-width: 0.3rem;
    }

    .loader-text {
        color: #2c3e50;
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
    }

    /* Alternative loader styles - uncomment to use */

    /*
.loader-spinner {
    width: 60px;
    height: 60px;
    margin: 0 auto;
}

.loader-spinner::after {
    content: '';
    display: block;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 6px solid #007bff;
    border-color: #007bff transparent #007bff transparent;
    animation: spin 1.2s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
*/
</style>
