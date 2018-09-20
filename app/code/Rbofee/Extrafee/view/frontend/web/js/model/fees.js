define(['ko'],
    function (ko) {
        return {
            fees: ko.observable([]),
            isLoading: ko.observable(false).extend({ rateLimit: 300 }),
            rejectFeesLoading: ko.observable(false)
        }
    }
);