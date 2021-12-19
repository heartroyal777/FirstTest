package com.nsa.dev_test.async;

import android.os.Handler;
import android.os.Looper;

import java.util.concurrent.Callable;
import java.util.concurrent.Executor;
import java.util.concurrent.Executors;

public class TaskRunner {

    private final Executor executor = Executors.newSingleThreadExecutor();
    private final Handler handler = new Handler(Looper.getMainLooper());

    public interface Callback<R> {
        void onTaskCompleted(R response);
		void onTaskFailure(String error);
    }

    public <R> void execute(final Callable<R> callable, final Callback<R> callback) {
        executor.execute(new Runnable(){
                @Override
                public void run() {
					// Background Task
                    try {
                        final R result = callable.call();
                        handler.post(new Runnable(){
                                @Override
                                public void run() {
									// on UI Task
                                    callback.onTaskCompleted(result);
                                }
                            });
                    } catch (Exception e) {
						callback.onTaskFailure(e.getMessage());
                    }       
                }   
            });

    }

}
