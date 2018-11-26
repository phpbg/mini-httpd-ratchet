new Vue({
    el: "#myFirstReactPhpApp",
    data: {
        wsUrl: globalWsUrl,
        message: null,
        messages: [],
        wsConnection: null,
        wsStatus: "Disconnected"
    },
    created: function() {
        var that = this;
        this.wsConnection = new WebSocket(this.wsUrl);
        this.wsConnection.onopen = function(e) {
            that.wsStatus = "Connected";
        };
        this.wsConnection.onerror = function(e) {
            that.wsStatus = "Error: "+e;
        };
        this.wsConnection.onclose = function(e) {
            that.wsStatus = "Disconnected";
        };
        this.wsConnection.onmessage = function(e) {
            that.messages.push({src: 'them', message: e.data});
        };
    },
    computed: {
        disableSend: function() {
            return (this.wsConnection == null || this.wsStatus !== "Connected");
        }
    },
    methods: {
        sendMessage: function() {
            this.messages.push({src: 'me', message: this.message});
            this.wsConnection.send(this.message);
            this.message = '';
        }
    },
    filters: {
        arrayToString: function(data) {
            return data.join("\r\n");
        }
    }
});